<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\utils\Utils;
use pocketmine\crafting\CraftingManager;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\cache\CraftingDataCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\recipe\FurnaceRecipe as ProtocolFurnaceRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeIngredient;
use pocketmine\network\mcpe\protocol\types\recipe\ShapedRecipe as ProtocolShapedRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe as ProtocolShapelessRecipe;
use pocketmine\Server;
use pocketmine\timings\Timings;
use pocketmine\utils\Binary;
use Ramsey\Uuid\Uuid;
use ReflectionException;
use ReflectionProperty;

class InventoryManager{

    const BREW_CONVERSATION = [
        426 => ItemIds::POTION,
        394 => ItemIds::GLOWSTONE_DUST,
        561 => ItemIds::SPLASH_POTION,
        562 => ItemIds::LINGERING_POTION,
        428 => ItemIds::FERMENTED_SPIDER_EYE,
        429 => ItemIds::BLAZE_POWDER,
        424 => ItemIds::GHAST_TEAR,
        283 => ItemIds::GOLDEN_CARROT,
        430 => ItemIds::MAGMA_CREAM,
        574 => ItemIds::PHANTOM_MEMBRANE,
        267 => ItemIds::PUFFERFISH,
        528 => ItemIds::RABBIT_FOOT,
        434 => ItemIds::REDSTONE_DUST,
        278 => ItemIds::GLISTERING_MELON,
        573 => ItemIds::TURTLE_HELMET,
        373 => ItemIds::REDSTONE_DUST,
        294 => ItemIds::NETHER_WART,
        328 => ItemIds::GUNPOWDER,
        560 => ItemIds::DRAGON_BREATH,
        416 => ItemIds::SUGAR
    ];

    /** @var PotionTypeRecipe[] */
    private array $potionTypeRecipes = [];
    /** @var PotionContainerChangeRecipe[] */
    private array $potionContainerRecipes = [];

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $manager = Server::getInstance()->getCraftingManager();
        $id = spl_object_id($manager);
        $reflection = new ReflectionProperty(CraftingDataCache::getInstance(), "caches");
        $reflection->setAccessible(true);
        $reflection->setValue($manager, [$id => $this->getCraftingDataPacket($manager)]);
    }

    private function getCraftingDataPacket(CraftingManager $manager): CraftingDataPacket{
        Timings::$craftingDataCacheRebuild->startTiming();
        $pk = new CraftingDataPacket();
        $pk->cleanRecipes = true;

        $counter = 0;
        $nullUUID = Uuid::fromString(Uuid::NIL);
        $converter = TypeConverter::getInstance();
        foreach($manager->getShapelessRecipes() as $list){
            foreach($list as $recipe){
                $pk->entries[] = new ProtocolShapelessRecipe(
                    CraftingDataPacket::ENTRY_SHAPELESS,
                    Binary::writeInt(++$counter),
                    array_map(function(Item $item) use ($converter) : RecipeIngredient{
                        return $converter->coreItemStackToRecipeIngredient($item);
                    }, $recipe->getIngredientList()),
                    array_map(function(Item $item) use ($converter) : ItemStack{
                        return $converter->coreItemStackToNet($item);
                    }, $recipe->getResults()),
                    $nullUUID,
                    "crafting_table",
                    50,
                    $counter
                );
            }
        }
        foreach($manager->getShapedRecipes() as $list){
            foreach($list as $recipe){
                $inputs = [];

                for($row = 0, $height = $recipe->getHeight(); $row < $height; ++$row){
                    for($column = 0, $width = $recipe->getWidth(); $column < $width; ++$column){
                        $inputs[$row][$column] = $converter->coreItemStackToRecipeIngredient($recipe->getIngredient($column, $row));
                    }
                }
                $pk->entries[] = $r = new ProtocolShapedRecipe(
                    CraftingDataPacket::ENTRY_SHAPED,
                    Binary::writeInt(++$counter),
                    $inputs,
                    array_map(function(Item $item) use ($converter) : ItemStack{
                        return $converter->coreItemStackToNet($item);
                    }, $recipe->getResults()),
                    $nullUUID,
                    "crafting_table",
                    50,
                    $counter
                );
            }
        }

        foreach($manager->getFurnaceRecipeManager()->getAll() as $recipe){
            $input = $converter->coreItemStackToNet($recipe->getInput());
            $pk->entries[] = new ProtocolFurnaceRecipe(
                CraftingDataPacket::ENTRY_FURNACE_DATA,
                $input->getId(),
                $input->getMeta(),
                $converter->coreItemStackToNet($recipe->getResult()),
                "furnace"
            );

            foreach(json_decode(file_get_contents(Utils::getResourceFile("brewing_recipes.json")), true) as $key => $i){
                $pk->potionTypeRecipes[] = new PotionTypeRecipe($i[0], $i[1], $i[2], $i[3], $i[4], $i[5]);
                $potion = new PotionTypeRecipe(self::convertPotionId($i[0]), self::convertPotionId($i[1]), self::convertPotionId($i[2]), self::convertPotionId($i[3]), self::convertPotionId($i[4]), self::convertPotionId($i[5]));
                $this->potionTypeRecipes[$potion->getInputItemId() . ":" . $potion->getInputItemMeta() . ":" . $potion->getIngredientItemId() . ":" . $potion->getIngredientItemMeta()] = clone $potion;
            }

            foreach([[426, 328, 561], [561, 560, 562]] as $key => $i){
                $pk->potionContainerRecipes[] = new PotionContainerChangeRecipe($i[0], $i[1], $i[2]);
                $potion = new PotionContainerChangeRecipe(self::convertPotionId($i[0]), self::convertPotionId($i[1]), self::convertPotionId($i[2]));
                $this->potionContainerRecipes[$potion->getInputItemId() . ":" . $potion->getIngredientItemId()] = clone $potion;
            }
        }

        Timings::$craftingDataCacheRebuild->stopTiming();
        return $pk;
    }

    public function getBrewingOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionTypeRecipes[$input->getId() . ":" . $input->getMeta() . ":" . $ingredient->getId() . ":" . $ingredient->getMeta()] ?? null;

        if($potion instanceof PotionTypeRecipe){
            return ItemFactory::getInstance()->get($potion->getOutputItemId(), $potion->getOutputItemMeta(), $input->getCount());
        }
        return null;
    }

    public function getBrewingContainerOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionContainerRecipes[$input->getId() . ":" . $ingredient->getId()] ?? null;

        if($potion instanceof PotionContainerChangeRecipe){
            return ItemFactory::getInstance()->get($potion->getOutputItemId(), $input->getMeta(), $input->getCount());
        }
        return null;
    }

    private static function convertPotionId(int $value): int{
        return self::BREW_CONVERSATION[$value] ?? $value;
    }

    public static function getExpForFurnace(Item $ingredient): float{
        switch($ingredient->getId()){
            case ItemIds::DIAMOND_ORE:
            case ItemIds::GOLD_ORE:
                case ItemIds::EMERALD_ORE;
                return 1;
            case ItemIds::IRON_ORE:
                return 0.7;
            case ItemIds::RAW_PORKCHOP:
            case ItemIds::RAW_BEEF:
            case ItemIds::RAW_CHICKEN:
            case ItemIds::RAW_FISH:
            case ItemIds::RAW_SALMON:
            case ItemIds::POTATO:
            case ItemIds::RAW_MUTTON:
            case ItemIds::RAW_RABBIT:
            case ItemIds::CLAY_BLOCK:
                return 0.35;
            case ItemIds::REDSTONE_ORE:
                return 0.3;
            case ItemIds::LAPIS_ORE:
            case ItemIds::NETHER_QUARTZ_ORE:
            case ItemIds::CLAY_BALL:
            case ItemIds::CACTUS:
                return 0.2;
            case ItemIds::LOG:
            case ItemIds::LOG2:
            case ItemIds::SPONGE:
                if($ingredient === ItemIds::SPONGE && $ingredient->getMeta() !== 1){
                    break;
                }
                return 0.15;
            case ItemIds::KELP:
            case ItemIds::COAL_ORE:
            case ItemIds::SAND:
            case ItemIds::SANDSTONE:
            case ItemIds::RED_SANDSTONE:
            case ItemIds::COBBLESTONE:
            case ItemIds::STONE:
            case ItemIds::QUARTZ_BLOCK:
            case ItemIds::NETHERRACK:
            case ItemIds::NETHER_BRICK:
            case ItemIds::STONE_BRICK:
            case ItemIds::TERRACOTTA:
            case ItemIds::CHORUS_FRUIT:
                return 0.1;
        }
        return 0;
    }
}
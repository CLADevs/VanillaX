<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\inventories\recipe\RecipesMap;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\cache\CraftingDataCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeIngredient;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\Server;
use pocketmine\utils\Binary;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;

class InventoryManager{
    use SingletonTrait;

    /** @var RecipesMap[] */
    private array $recipes = [];

    public function __construct(){
        self::setInstance($this);
        TypeConverter::setInstance(new TypeConverterX());
    }

    public function startup(): void{
        $this->registerRecipes("smithing_table");
    }

    private function registerRecipes(string $type): bool{
        $content = @file_get_contents(Utils::getResourceFile("recipes/$type.json"));

        if($content){
           $map = RecipesMap::from(json_decode($content, true));
           $cache = CraftingDataCache::getInstance()->getCache(Server::getInstance()->getCraftingManager());

           if($map->isShapeless()){
               $this->registerShapeless($cache, $map);
           }
           $this->recipes[$map->getType()] = $map;
        }
        return false;
    }

    private function registerShapeless(CraftingDataPacket $cache, RecipesMap $map): void{
        $counter = count($cache->recipesWithTypeIds);
        $converter = TypeConverterX::getInstance();
        $nullUUID = Uuid::fromString(Uuid::NIL);

        foreach($map->getRecipes() as $recipe){
            $cache->recipesWithTypeIds[] = new ShapelessRecipe(
                CraftingDataPacket::ENTRY_SHAPELESS,
                Binary::writeInt(++$counter),
                array_map(function(Item $item) use ($converter) : RecipeIngredient{
                    return $converter->coreItemStackToRecipeIngredient($item);
                }, [$recipe->getInput(), $recipe->getMaterial()]),
                array_map(function(Item $item) use ($converter) : ItemStack{
                    return $converter->coreItemStackToNet($item);
                }, [$recipe->getOutput()]),
                $nullUUID,
                $map->getType(),
                50,
                $counter
            );
        }
    }

    public function getExpForFurnace(Item $ingredient): float{
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
                if($ingredient->getId() === ItemIds::SPONGE && $ingredient->getMeta() !== 1){
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

    public function getComposterChance(Item $ingredient): int{
        switch($ingredient->getId()){
            case ItemIds::BEETROOT_SEEDS:
            case ItemIds::DRIED_KELP:
            case ItemIds::KELP;
            case ItemIds::GRASS;
            case ItemIds::LEAVES;
            case ItemIds::MELON_SEEDS;
            case ItemIds::PUMPKIN_SEEDS;
            case ItemIds::SAPLING;
            case ItemIds::SEAGRASS;
            case ItemIds::SWEET_BERRIES;
            case ItemIds::WHEAT_SEEDS;
                return 30;
            case ItemIds::CACTUS:
            case ItemIds::DRIED_KELP_BLOCK:
            case ItemIds::MELON_SLICE:
            case ItemIds::SUGARCANE:
            case ItemIds::TALL_GRASS:
            case ItemIds::VINES:
            case LegacyItemIds::WEEPING_VINES:
            case LegacyItemIds::TWISTING_VINES:
                return 50;
            case ItemIds::APPLE:
            case ItemIds::BEETROOT:
            case ItemIds::CARROT:
            case ItemIds::COCOA:
            case ItemIds::RED_FLOWER:
            case ItemIds::YELLOW_FLOWER:
            case ItemIds::LILY_PAD:
            case ItemIds::MELON:
            case ItemIds::RED_MUSHROOM:
            case ItemIds::BROWN_MUSHROOM:
            case ItemIds::MUSHROOM_STEW:
            case ItemIds::NETHER_WART:
            case ItemIds::POTATO:
            case ItemIds::PUMPKIN:
            case ItemIds::SEA_PICKLE:
            case ItemIds::WHEAT:
            case LegacyItemIds::CRIMSON_FUNGUS:
            case LegacyItemIds::WARPED_FUNGUS:
            case LegacyItemIds::CRIMSON_ROOTS:
            case LegacyItemIds::WARPED_ROOTS:
                return 65;
            case ItemIds::BAKED_POTATO:
            case ItemIds::BREAD:
            case ItemIds::COOKIE:
            case ItemIds::HAY_BALE:
            case ItemIds::BROWN_MUSHROOM_BLOCK:
            case ItemIds::NETHER_WART_BLOCK:
            case LegacyItemIds::WARPED_WART_BLOCK:
                return 85;
            case ItemIds::CAKE:
            case ItemIds::PUMPKIN_PIE:
                return 100;
        }
        return 0;
    }

    /**
     * @return RecipesMap[]
     */
    public function getRecipes(): array{
        return $this->recipes;
    }
}

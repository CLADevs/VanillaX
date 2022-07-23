<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\inventories\recipe\RecipesMap;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\block\Planks;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\network\mcpe\cache\CraftingDataCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeIngredient;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeWithTypeId;
use pocketmine\network\mcpe\protocol\types\recipe\ShapedRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\Server;
use pocketmine\utils\Binary;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;

class InventoryManager{
    use SingletonTrait;

    /** @var RecipesMap[] */
    private array $recipes = [];
    /** @var RecipeWithTypeId[] */
    private array $recipeNetIds = [];

    public function __construct(){
        self::setInstance($this);
        TypeConverter::setInstance(new TypeConverterX());
    }

    public function startup(): void{
        $this->registerRecipes("smithing_table");

        $cache = CraftingDataCache::getInstance()->getCache(Server::getInstance()->getCraftingManager());
        foreach($cache->recipesWithTypeIds as $recipe){
            if($recipe instanceof ShapelessRecipe || $recipe instanceof ShapedRecipe){
                $this->recipeNetIds[$recipe->getRecipeNetId()] = $recipe;
            }
        }
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

    public function isRepairable(Item $input, Item $material): bool{
        if($input instanceof ToolTier){
            switch($input->name()){
                case "wood":
                    return $material instanceof Planks;
                case "stone":
                    return $material->getId() === ItemIds::COBBLESTONE;
                case "iron":
                    return $material->getId() === ItemIds::IRON_INGOT;
                case "gold":
                    return $material->getId() === ItemIds::GOLD_INGOT;
                case "diamond":
                    return $material->getId() === ItemIds::DIAMOND;
            }
        }
        switch($input->getId()){
            case ItemIds::SHIELD:
                return $material instanceof Planks;
            case ItemIds::LEATHER_CAP:
            case ItemIds::LEATHER_CHESTPLATE:
            case ItemIds::LEATHER_LEGGINGS:
            case ItemIds::LEATHER_BOOTS:
                return $material->getId() === ItemIds::LEATHER;
            case ItemIds::IRON_HELMET:
            case ItemIds::IRON_CHESTPLATE:
            case ItemIds::IRON_LEGGINGS:
            case ItemIds::IRON_BOOTS:
                return $material->getId() === ItemIds::IRON_INGOT;
            case ItemIds::GOLD_HELMET:
            case ItemIds::GOLD_CHESTPLATE:
            case ItemIds::GOLD_LEGGINGS:
            case ItemIds::GOLD_BOOTS:
                return $material->getId() === ItemIds::GOLD_INGOT;
            case ItemIds::DIAMOND_HELMET:
            case ItemIds::DIAMOND_CHESTPLATE:
            case ItemIds::DIAMOND_LEGGINGS:
            case ItemIds::DIAMOND_BOOTS:
                return $material->getId() === ItemIds::DIAMOND;
            case LegacyItemIds::NETHERITE_HELMET:
            case LegacyItemIds::NETHERITE_CHESTPLATE:
            case LegacyItemIds::NETHERITE_LEGGINGS:
            case LegacyItemIds::NETHERITE_BOOTS:
            case LegacyItemIds::NETHERITE_AXE:
            case LegacyItemIds::NETHERITE_PICKAXE:
            case LegacyItemIds::NETHERITE_HOE:
            case LegacyItemIds::NETHERITE_SHOVEL:
            case LegacyItemIds::NETHERITE_SWORD:
                return $material->getId() === LegacyItemIds::NETHERITE_INGOT;
            case ItemIds::TURTLE_SHELL_PIECE:
                return $material->getId() === LegacyItemIds::SCUTE;
            case ItemIds::ELYTRA:
                return $material->getId() === ItemIds::PHANTOM_MEMBRANE;
        }
        return $input->equals($material, false);
    }

    public function getRecipeByNetId(int $netId): ?RecipeWithTypeId{
        return $this->recipeNetIds[$netId] ?? null;
    }

    /**
     * @return RecipesMap[]
     */
    public function getRecipes(): array{
        return $this->recipes;
    }
}

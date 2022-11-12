<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\inventories\recipe\RecipesMap;
use CLADevs\VanillaX\inventories\types\SmithingInventory;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
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
    }

    public function startup(): void{
        $this->registerRecipes(SmithingInventory::BLOCK_NAME);

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
        $converter = TypeConverter::getInstance();
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
        return match ($ingredient->getId()){
            ItemIds::BEETROOT_SEEDS, ItemIds::DRIED_KELP, ItemIds::KELP, ItemIds::GRASS, ItemIds::LEAVES, ItemIds::MELON_SEEDS, ItemIds::PUMPKIN_SEEDS, ItemIds::SAPLING, ItemIds::SEAGRASS, ItemIds::SWEET_BERRIES, ItemIds::WHEAT_SEEDS => 30,
            ItemIds::CACTUS, ItemIds::DRIED_KELP_BLOCK, ItemIds::MELON_SLICE, ItemIds::SUGARCANE, ItemIds::TALL_GRASS, ItemIds::VINES, LegacyItemIds::WEEPING_VINES, LegacyItemIds::TWISTING_VINES => 50,
            ItemIds::APPLE, ItemIds::BEETROOT, ItemIds::CARROT, ItemIds::COCOA, ItemIds::RED_FLOWER, ItemIds::YELLOW_FLOWER, ItemIds::LILY_PAD, ItemIds::MELON, ItemIds::RED_MUSHROOM, ItemIds::BROWN_MUSHROOM, ItemIds::MUSHROOM_STEW, ItemIds::NETHER_WART, ItemIds::POTATO, ItemIds::PUMPKIN, ItemIds::SEA_PICKLE, ItemIds::WHEAT, LegacyItemIds::CRIMSON_FUNGUS, LegacyItemIds::WARPED_FUNGUS, LegacyItemIds::CRIMSON_ROOTS, LegacyItemIds::WARPED_ROOTS => 65,
            ItemIds::BAKED_POTATO, ItemIds::BREAD, ItemIds::COOKIE, ItemIds::HAY_BALE, ItemIds::BROWN_MUSHROOM_BLOCK, ItemIds::NETHER_WART_BLOCK, LegacyItemIds::WARPED_WART_BLOCK => 85,
            ItemIds::CAKE, ItemIds::PUMPKIN_PIE => 100,
            default => 0,
        };
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

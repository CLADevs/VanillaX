<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\utils\SingletonTrait;

class InventoryManager{
    use SingletonTrait;

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

    public function __construct(){
        self::setInstance($this);
        TypeConverter::setInstance(new TypeConverterX());
    }

    /**
     * @param PotionTypeRecipe[] $potionTypeRecipes
     */
    public function setPotionTypeRecipes(array $potionTypeRecipes): void{
        $this->potionTypeRecipes = $potionTypeRecipes;
    }

    /**
     * @param PotionContainerChangeRecipe[] $potionContainerRecipes
     */
    public function setPotionContainerRecipes(array $potionContainerRecipes): void{
        $this->potionContainerRecipes = $potionContainerRecipes;
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

    public static function convertPotionId(int $value): int{
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
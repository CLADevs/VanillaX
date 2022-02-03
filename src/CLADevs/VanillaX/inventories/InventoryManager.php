<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\utils\SingletonTrait;

class InventoryManager{
    use SingletonTrait;

    /** @var PotionTypeRecipe[] */
    private array $potionTypeRecipes = [];
    /** @var PotionContainerChangeRecipe[] */
    private array $potionContainerRecipes = [];

    public function __construct(){
        self::setInstance($this);
        TypeConverter::setInstance(new TypeConverterX());
    }

    /**
     * @return PotionTypeRecipe[]
     */
    public function getPotionTypeRecipes(): array{
        return $this->potionTypeRecipes;
    }

    /**
     * @param PotionTypeRecipe[] $potionTypeRecipes
     */
    public function setPotionTypeRecipes(array $potionTypeRecipes): void{
        $this->potionTypeRecipes = $potionTypeRecipes;
    }

    /**
     * @return PotionContainerChangeRecipe[]
     */
    public function getPotionContainerRecipes(): array{
        return $this->potionContainerRecipes;
    }

    /**
     * @param PotionContainerChangeRecipe[] $potionContainerRecipes
     */
    public function setPotionContainerRecipes(array $potionContainerRecipes): void{
        $this->potionContainerRecipes = $potionContainerRecipes;
    }

    public function getBrewingOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionTypeRecipes[self::hashPotionType($ingredient->getId(), $ingredient->getMeta(), $input->getId(), $input->getMeta())] ?? null;

        if($potion instanceof PotionTypeRecipe){
            return ItemFactory::getInstance()->get($potion->getOutputItemId(), $potion->getOutputItemMeta(), $input->getCount());
        }
        return null;
    }

    public function getBrewingContainerOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionContainerRecipes[self::hashPotionContainer($ingredient->getId(), $input->getId())] ?? null;

        if($potion instanceof PotionContainerChangeRecipe){
            return ItemFactory::getInstance()->get($potion->getOutputItemId(), $input->getMeta(), $input->getCount());
        }
        return null;
    }

    public function internalPotionTypeRecipe(PotionTypeRecipe $recipe): PotionTypeRecipe{
        [$inputId, $inputMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getInputItemId(), $recipe->getInputItemMeta());
        [$ingredientId, $ingredientMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getIngredientItemId(), $recipe->getIngredientItemMeta());
        [$outputId, $outputMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getOutputItemId(), $recipe->getOutputItemMeta());
        return new PotionTypeRecipe($inputId, $inputMeta, $ingredientId, $ingredientMeta, $outputId, $outputMeta);
    }

    public function internalPotionContainerRecipe(PotionContainerChangeRecipe $recipe): PotionContainerChangeRecipe{
        $inputId = ItemTranslator::getInstance()->fromNetworkId($recipe->getInputItemId(), 0)[0];
        $ingredientId = ItemTranslator::getInstance()->fromNetworkId($recipe->getIngredientItemId(), 0)[0];
        $outputId = ItemTranslator::getInstance()->fromNetworkId($recipe->getOutputItemId(), 0)[0];
        return new PotionContainerChangeRecipe($inputId, $ingredientId, $outputId);
    }

    public function hashPotionType(PotionTypeRecipe|int $ingredientId, int $ingredientMeta = 0, int $inputId = 0, int $inputMeta = 0): string{
        $recipe = $ingredientId;

        if($recipe instanceof PotionTypeRecipe){
            $ingredientId = $recipe->getIngredientItemId();
            $ingredientMeta = $recipe->getIngredientItemMeta();
            $inputId = $recipe->getInputItemId();
            $inputMeta = $recipe->getInputItemMeta();
        }
        return $ingredientId + $ingredientMeta + $inputId + $inputMeta;
    }

    public function hashPotionContainer(PotionContainerChangeRecipe|int $ingredientId, int $inputId = 0): string{
        $recipe = $ingredientId;

        if($recipe instanceof PotionContainerChangeRecipe){
            $ingredientId = $recipe->getIngredientItemId();
            $inputId = $recipe->getInputItemId();
        }
        return $ingredientId + $inputId;
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
            case LegacyItemIds::NETHER_WART_BLOCK:
            case LegacyItemIds::WARPED_WART_BLOCK:
                return 85;
            case ItemIds::CAKE:
            case ItemIds::PUMPKIN_PIE:
                return 100;
        }
        return 0;
    }

}
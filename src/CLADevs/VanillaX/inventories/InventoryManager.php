<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;
use pocketmine\Server;
use pocketmine\timings\Timings;
use ReflectionException;
use ReflectionProperty;
use const pocketmine\RESOURCE_PATH;

class InventoryManager{

    /** @var PotionTypeRecipe[] */
    private array $potionTypeRecipes = [];
    /** @var PotionContainerChangeRecipe[] */
    private array $potionContainerRecipes = [];

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $reflection = new ReflectionProperty($craftingManager = Server::getInstance()->getCraftingManager(), "craftingDataCache");
        $reflection->setAccessible(true);
        $reflection->setValue($craftingManager, $this->getCraftingDataPacket());
    }

    private function getCraftingDataPacket(): BatchPacket{
        Timings::$craftingDataCacheRebuildTimer->startTiming();
        $manager = Server::getInstance()->getCraftingManager();
        $pk = new CraftingDataPacket();
        $pk->cleanRecipes = true;

        foreach($manager->getShapelessRecipes() as $list){
            foreach($list as $recipe){
                $pk->addShapelessRecipe($recipe);
            }
        }

        foreach($manager->getShapedRecipes() as $list){
            foreach($list as $recipe){
                $pk->addShapedRecipe($recipe);
            }
        }

        foreach($manager->getFurnaceRecipes() as $recipe){
            $pk->addFurnaceRecipe($recipe);
        }

        $recipes = json_decode(file_get_contents(RESOURCE_PATH . "vanilla" . DIRECTORY_SEPARATOR . "recipes.json"), true);

        foreach($recipes["potion_type"] as $recipe){
            [$inputNetId, $inputNetDamage] = ItemTranslator::getInstance()->toNetworkId($recipe["input"]["id"], $recipe["input"]["damage"] ?? 0);
            [$ingredientNetId, $ingredientNetDamage] = ItemTranslator::getInstance()->toNetworkId($recipe["ingredient"]["id"], $recipe["ingredient"]["damage"] ?? 0);
            [$outputNetId, $outputNetDamage] = ItemTranslator::getInstance()->toNetworkId($recipe["output"]["id"], $recipe["output"]["damage"] ?? 0);
            $potion = new PotionTypeRecipe($inputNetId, $inputNetDamage, $ingredientNetId, $ingredientNetDamage, $outputNetId, $outputNetDamage);
            $pk->potionTypeRecipes[] = $potion;
            $potion = $this->internalPotionTypeRecipe(clone $potion);
            $this->potionTypeRecipes[self::hashPotionType($potion)] = $potion;
        }

        foreach($recipes["potion_container_change"] as $recipe){
            $inputNetId = ItemTranslator::getInstance()->toNetworkId($recipe["input_item_id"], 0)[0];
            $ingredientNetId = ItemTranslator::getInstance()->toNetworkId($recipe["ingredient"]["id"], 0)[0];
            $outputNetId = ItemTranslator::getInstance()->toNetworkId($recipe["output_item_id"], 0)[0];
            $potion = new PotionContainerChangeRecipe($inputNetId, $ingredientNetId, $outputNetId);
            $pk->potionContainerRecipes[] = $potion;
            $potion = $this->internalPotionContainerRecipe(clone $potion);
            $this->potionContainerRecipes[self::hashPotionContainer($potion)] = $potion;
        }
        $pk->encode();

        $batch = new BatchPacket();
        $batch->addPacket($pk);
        $batch->setCompressionLevel(Server::getInstance()->networkCompressionLevel);
        $batch->encode();
        Timings::$craftingDataCacheRebuildTimer->stopTiming();
        return $batch;
    }

    public function getBrewingOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionTypeRecipes[self::hashPotionType($ingredient->getId(), $ingredient->getDamage(), $input->getId(), $input->getDamage())] ?? null;

        if($potion instanceof PotionTypeRecipe){
            return ItemFactory::get($potion->getOutputItemId(), $potion->getOutputItemMeta(), $input->getCount());
        }
        return null;
    }

    public function getBrewingContainerOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionContainerRecipes[self::hashPotionContainer($ingredient->getId(), $input->getId())] ?? null;

        if($potion instanceof PotionContainerChangeRecipe){
            return ItemFactory::get($potion->getOutputItemId(), $input->getDamage(), $input->getCount());
        }
        return null;
    }

    private function internalPotionTypeRecipe(PotionTypeRecipe $recipe): PotionTypeRecipe{
        [$inputId, $inputMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getInputItemId(), $recipe->getInputItemMeta());
        [$ingredientId, $ingredientMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getIngredientItemId(), $recipe->getIngredientItemMeta());
        [$outputId, $outputMeta] = ItemTranslator::getInstance()->fromNetworkId($recipe->getOutputItemId(), $recipe->getOutputItemMeta());
        return new PotionTypeRecipe($inputId, $inputMeta, $ingredientId, $ingredientMeta, $outputId, $outputMeta);
    }

    private function internalPotionContainerRecipe(PotionContainerChangeRecipe $recipe): PotionContainerChangeRecipe{
        $inputId = ItemTranslator::getInstance()->fromNetworkId($recipe->getInputItemId(), 0)[0];
        $ingredientId = ItemTranslator::getInstance()->fromNetworkId($recipe->getIngredientItemId(), 0)[0];
        $outputId = ItemTranslator::getInstance()->fromNetworkId($recipe->getOutputItemId(), 0)[0];
        return new PotionContainerChangeRecipe($inputId, $ingredientId, $outputId);
    }

    /**
     * @param PotionTypeRecipe|int $ingredientId
     * @param int $ingredientMeta
     * @param int $inputId
     * @param int $inputMeta
     * @return string
     */
    public function hashPotionType($ingredientId, int $ingredientMeta = 0, int $inputId = 0, int $inputMeta = 0): string{
        $recipe = $ingredientId;

        if($recipe instanceof PotionTypeRecipe){
            $ingredientId = $recipe->getIngredientItemId();
            $ingredientMeta = $recipe->getIngredientItemMeta();
            $inputId = $recipe->getInputItemId();
            $inputMeta = $recipe->getInputItemMeta();
        }
        return $ingredientId + $ingredientMeta + $inputId + $inputMeta;
    }

    /**
     * @param PotionContainerChangeRecipe|int $ingredientId
     * @param int $inputId
     * @return string
     */
    public function hashPotionContainer($ingredientId, int $inputId = 0): string{
        $recipe = $ingredientId;
        
        if($recipe instanceof PotionContainerChangeRecipe){
            $ingredientId = $recipe->getIngredientItemId();
            $inputId = $recipe->getInputItemId();
        }
        return $ingredientId + $inputId;
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
                if($ingredient === ItemIds::SPONGE && $ingredient->getDamage() !== 1){
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
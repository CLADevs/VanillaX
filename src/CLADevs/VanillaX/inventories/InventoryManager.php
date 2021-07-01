<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;
use pocketmine\Server;
use pocketmine\timings\Timings;
use ReflectionException;
use ReflectionProperty;

class InventoryManager{

    const BREW_CONVERSATION = [
        424 => ItemIds::POTION,
        392 => ItemIds::GLOWSTONE_DUST,
        551 => ItemIds::SPLASH_POTION,
        552 => ItemIds::LINGERING_POTION,
        426 => ItemIds::FERMENTED_SPIDER_EYE,
        427 => ItemIds::BLAZE_POWDER,
        422 => ItemIds::GHAST_TEAR,
        283 => ItemIds::GOLDEN_CARROT,
        428 => ItemIds::MAGMA_CREAM,
        564 => ItemIds::PHANTOM_MEMBRANE,
        267 => ItemIds::PUFFERFISH,
        518 => ItemIds::RABBIT_FOOT,
        432 => ItemIds::REDSTONE_DUST,
        278 => ItemIds::GLISTERING_MELON,
        563 => ItemIds::TURTLE_HELMET,
        371 => ItemIds::REDSTONE_DUST,
        294 => ItemIds::NETHER_WART,
        328 => ItemIds::GUNPOWDER,
        550 => ItemIds::DRAGON_BREATH,
        414 => ItemIds::SUGAR
    ];

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

        foreach(json_decode(file_get_contents(Utils::getResourceFile("brewing_recipes.json")), true) as $key => $i){
            $pk->potionTypeRecipes[] = new PotionTypeRecipe($i[0], $i[1], $i[2], $i[3], $i[4], $i[5]);
            $potion = new PotionTypeRecipe(self::convertPotionId($i[0]), self::convertPotionId($i[1]), self::convertPotionId($i[2]), self::convertPotionId($i[3]), self::convertPotionId($i[4]), self::convertPotionId($i[5]));
            $this->potionTypeRecipes[$potion->getInputItemId() . ":" . $potion->getInputItemMeta() . ":" . $potion->getIngredientItemId() . ":" . $potion->getIngredientItemMeta()] = clone $potion;
        }

        foreach([[424 ,328 ,551] ,[551 ,550 ,552]] as $key => $i){
            $pk->potionContainerRecipes[] = new PotionContainerChangeRecipe($i[0], $i[1], $i[2]);
            $potion = new PotionContainerChangeRecipe(self::convertPotionId($i[0]), self::convertPotionId($i[1]), self::convertPotionId($i[2]));
            $this->potionContainerRecipes[$potion->getInputItemId() . ":" . $potion->getIngredientItemId()] = clone $potion;
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
        $potion = $this->potionTypeRecipes[$input->getId() . ":" . $input->getDamage() . ":" . $ingredient->getId() . ":" . $ingredient->getDamage()] ?? null;

        if($potion instanceof PotionTypeRecipe){
            return ItemFactory::get($potion->getOutputItemId(), $potion->getOutputItemMeta(), $input->getCount());
        }
        return null;
    }

    public function getBrewingContainerOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionContainerRecipes[$input->getId() . ":" . $ingredient->getId()] ?? null;

        if($potion instanceof PotionContainerChangeRecipe){
            return ItemFactory::get($potion->getOutputItemId(), $input->getDamage(), $input->getCount());
        }
        return null;
    }

    private static function convertPotionId(int $value): int{
        return self::BREW_CONVERSATION[$value] ?? $value;
    }
}
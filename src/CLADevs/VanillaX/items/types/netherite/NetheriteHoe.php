<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Hoe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteHoe extends Hoe{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_HOE, 0, "Netherite Hoe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency() : float{
        return 12; //Netherite Hoe Speed
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_HOE),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_HOE, 0, 1)
        ]);
    }
}
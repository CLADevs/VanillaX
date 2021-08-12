<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Axe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteAxe extends Axe{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_AXE, 0, "Netherite Axe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 8; //Netherite Axe Damage
    }

    public function getMaxDurability(): int{
        return 2031;
    }

    protected function getBaseMiningEfficiency(): float{
        return 9;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_AXE),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_AXE, 0, 1)
        ]);
    }
}
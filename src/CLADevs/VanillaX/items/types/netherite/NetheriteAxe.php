<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\item\Axe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;

class NetheriteAxe extends Axe{
    use RecipeItemTrait;

    //Netherite Tier isnt a thing
    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_AXE, 0), "Netherite Axe", ToolTier::DIAMOND());
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
            VanillaItems::DIAMOND_AXE(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_AXE)
        ]);
    }
}
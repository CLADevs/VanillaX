<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;

class NetheritePickaxe extends Pickaxe{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_PICKAXE, 0, "Netherite Pickaxe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 7; //Netherite Pickaxe Damage
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float{
        return 10;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_PICKAXE),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_PICKAXE, 0, 1)
        ]);
    }
}
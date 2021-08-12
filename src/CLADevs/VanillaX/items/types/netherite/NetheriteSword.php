<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Sword;

class NetheriteSword extends Sword{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_SWORD, 0, "Netherite Sword", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints() : int{
        return 9; //9 damage for Netherite Sword
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_SWORD),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_SWORD, 0, 1)
        ]);
    }
}
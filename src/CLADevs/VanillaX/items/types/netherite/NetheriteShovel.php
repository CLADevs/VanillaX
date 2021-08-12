<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Shovel;

class NetheriteShovel extends Shovel{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_SHOVEL, 0, "Netherite Shovel", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 6; //Netherite Shovel Damage
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float{
        return 9;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_SHOVEL),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_SHOVEL, 0, 1)
        ]);
    }
}
<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Shovel;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;

class NetheriteShovel extends Shovel{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_SHOVEL, 0), "Netherite Shovel", ToolTier::DIAMOND());
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
            VanillaItems::DIAMOND_SHOVEL(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_SHOVEL)
        ]);
    }
}
<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Armor;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteChestplate extends Armor{
    use RecipeItemTrait;

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_CHESTPLATE, $meta, "Netherite Chestplate");
    }

    public function getDefensePoints(): int{
        return 8;
    }

    public function getMaxDurability(): int{
        return 593;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_CHESTPLATE),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_CHESTPLATE, 0, 1)
        ]);
    }
}

<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Armor;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteLeggings extends Armor{
    use RecipeItemTrait;

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_LEGGINGS, $meta, "Netherite Leggings");
    }

    public function getDefensePoints(): int{
        return 6;
    }

    public function getMaxDurability(): int{
        return 556;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_LEGGINGS),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_LEGGINGS, 0, 1)
        ]);
    }
}

<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Armor;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteHelmet extends Armor{
    use RecipeItemTrait;

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_HELMET, $meta, "Netherite Helmet");
    }

    public function getDefensePoints(): int{
        return 3;
    }

    public function getMaxDurability(): int{
        return 408;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_HELMET),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_HELMET, 0, 1)
        ]);
    }
}

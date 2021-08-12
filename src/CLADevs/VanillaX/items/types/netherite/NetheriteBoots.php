<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Armor;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class NetheriteBoots extends Armor{
    use RecipeItemTrait;

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_BOOTS, $meta, "Netherite Boots");
    }

    public function getDefensePoints(): int{
        return 3;
    }

    public function getMaxDurability(): int{
        return 482;
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            ItemFactory::get(ItemIds::DIAMOND_BOOTS),
            ItemFactory::get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::get(ItemIdentifiers::NETHERITE_BOOTS, 0, 1)
        ]);
    }
}

<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;

class NetheriteLeggings extends Armor{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_LEGGINGS, 0), "Netherite Leggings", new ArmorTypeInfo(6, 556, ArmorInventory::SLOT_LEGS));
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            VanillaItems::DIAMOND_LEGGINGS(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_LEGGINGS)
        ]);
    }
}

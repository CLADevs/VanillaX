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

class NetheriteChestplate extends Armor{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_CHESTPLATE, 0), "Netherite Chestplate", new ArmorTypeInfo(5, 593, ArmorInventory::SLOT_CHEST));
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            VanillaItems::DIAMOND_CHESTPLATE(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_CHESTPLATE, 0, 1)
        ]);
    }
}

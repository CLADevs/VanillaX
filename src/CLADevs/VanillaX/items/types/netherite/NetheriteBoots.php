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

class NetheriteBoots extends Armor{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_BOOTS, 0), "Netherite Boots", new ArmorTypeInfo(3, 482, ArmorInventory::SLOT_FEET));
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            VanillaItems::DIAMOND_BOOTS(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_BOOTS, 0, 1)
        ]);
    }
}

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

class NetheriteHelmet extends Armor{
    use RecipeItemTrait;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_HELMET, 0), "Netherite Helmet", new ArmorTypeInfo(3, 408, ArmorInventory::SLOT_HEAD));
    }

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return new ShapelessRecipe([
            VanillaItems::DIAMOND_HELMET(),
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_INGOT)
        ], [
            ItemFactory::getInstance()->get(ItemIdentifiers::NETHERITE_HELMET)
        ]);
    }
}

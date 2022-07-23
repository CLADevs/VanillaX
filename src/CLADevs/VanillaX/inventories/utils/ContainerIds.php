<?php

namespace CLADevs\VanillaX\inventories\utils;

//ids from dragonfly and nukkit
use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use pocketmine\block\inventory\CraftingTableInventory;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

class ContainerIds{

    const ANVIL_INPUT = 0;
    const ANVIL_MATERIAL = 1;
    const ANVIL_RESULT = 2;
    const SMITHING_INPUT = 3;
    const SMITHING_MATERIAL = 4;
    const SMITHING_RESULT = 5;
    const ARMOR = 6;
    const CONTAINER = 7;
    const BEACON_PAYMENT = 8;
    const BREWING_INPUT = 9;
    const BREWING_RESULT = 10;
    const BREWING_FUEL = 11;
    const FULL_INVENTORY = 12;
    const CRAFTING_INPUT = 13;
    const CRAFTING_OUTPUT = 14;
    const ENCHANTING_INPUT = 21;
    const ENCHANTING_MATERIAL = 22;
    const FURNACE_FUEL = 23;
    const FURNACE_INPUT = 24;
    const FURNACE_RESULT = 25;
    const HORSE_EQUIP = 26;
    const HOTBAR = 27;
    const INVENTORY = 28;
    const SHULKER_BOX = 29;
    const TRADE_INPUT_A = 30;
    const TRADE_INPUT_B = 31;
    const TRADE_INPUT_RESULT = 32;
    const OFFHAND = 33;
    const LOOM_INPUT = 40;
    const LOOM_DYE = 41;
    const LOOM_MATERIAL = 42;
    const LOOM_RESULT = 43;
    const BLAST_FURNACE_INPUT = 44;
    const SMOKER_INPUT = 45;
    const TRADE2_INPUT_A = 46;
    const TRADE2_INPUT_B = 47;
    const TRADE2_RESULT = 48;
    const GRINDSTONE_INPUT = 49;
    const GRINDSTONE_ADDITIONAL = 50;
    const GRINDSTONE_RESULT = 51;
    const STONECUTTER_INPUT = 52;
    const STONECUTTER_RESULT = 53;
    const CARTOGRAPHY_INPUT = 54;
    const CARTOGRAPHY_ADDITIONAL = 55;
    const CARTOGRAPHY_RESULT = 56;
    const BARREL = 57;
    const CURSOR = 58;
    const CREATIVE_OUTPUT = 59;

    public static function getInventory(int $containerId, Player $player): ?Inventory{
        $currentInventory = $player->getCurrentWindow();

        switch($containerId){
            case self::OFFHAND:
                return $player->getOffHandInventory();
            case self::CURSOR:
                return $player->getCursorInventory();
            case self::ARMOR:
                return $player->getArmorInventory();
            case self::HOTBAR:
            case self::INVENTORY:
            case self::FULL_INVENTORY:
                return $player->getInventory();
            case self::CRAFTING_INPUT:
            case self::CRAFTING_OUTPUT:
            case self::CREATIVE_OUTPUT:
                if($currentInventory instanceof CraftingTableInventory){
                    return $currentInventory;
                }
                return $player->getCraftingGrid();
            case self::ANVIL_INPUT:
            case self::ANVIL_MATERIAL:
            case self::ANVIL_RESULT:
                if($currentInventory instanceof AnvilInventory){
                    return $currentInventory;
                }
                break;
            case self::ENCHANTING_INPUT:
            case self::ENCHANTING_MATERIAL:
                if($currentInventory instanceof EnchantInventory){
                    return $currentInventory;
                }
                break;
        }
        return null;
    }
}
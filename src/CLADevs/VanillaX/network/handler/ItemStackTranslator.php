<?php

namespace CLADevs\VanillaX\network\handler;

use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\types\SmithingInventory;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use pocketmine\block\inventory\CraftingTableInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerCraftingInventory;
use pocketmine\inventory\PlayerOffHandInventory;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerUIIds;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\player\Player;

class ItemStackTranslator{

    public static function translateSlot(int $index, Inventory $inventory): int{
        $slotMap = match(true){
            $inventory instanceof PlayerCraftingInventory => UIInventorySlotOffset::CRAFTING2X2_INPUT,
            $inventory instanceof CraftingTableInventory => UIInventorySlotOffset::CRAFTING3X3_INPUT,
            $inventory instanceof AnvilInventory => UIInventorySlotOffset::ANVIL,
            $inventory instanceof EnchantInventory => UIInventorySlotOffset::ENCHANTING_TABLE,
            $inventory instanceof TradeInventory => UIInventorySlotOffset::TRADE2_INGREDIENT,
            $inventory instanceof BeaconInventory => [UIInventorySlotOffset::BEACON_PAYMENT => 0],
            $inventory instanceof SmithingInventory => UIInventorySlotOffset::SMITHING_TABLE,
            $inventory instanceof PlayerOffHandInventory => [1 => 0],
            default => null
        };
        if($slotMap !== null){
            $index = $slotMap[$index] ?? $index;
        }
        return $index;
    }

    public static function translateContainerId(Player $player, int $containerId): ?Inventory{
        $currentInventory = $player->getCurrentWindow();

        switch($containerId){
            case ContainerUIIds::OFFHAND:
                return $player->getOffHandInventory();
            case ContainerUIIds::CURSOR:
                return $player->getCursorInventory();
            case ContainerUIIds::ARMOR:
                return $player->getArmorInventory();
            case ContainerUIIds::HOTBAR:
            case ContainerUIIds::INVENTORY:
            case ContainerUIIds::COMBINED_HOTBAR_AND_INVENTORY:
                return $player->getInventory();
            case ContainerUIIds::CRAFTING_INPUT:
            case ContainerUIIds::CRAFTING_OUTPUT_PREVIEW:
            case ContainerUIIds::CREATED_OUTPUT:
                if($currentInventory instanceof CraftingTableInventory){
                    return $currentInventory;
                }
                return $player->getCraftingGrid();
        }
        return $currentInventory;
    }
}
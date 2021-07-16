<?php

namespace CLADevs\VanillaX\inventories\utils;

use CLADevs\VanillaX\inventories\actions\EnchantItemAction;
use CLADevs\VanillaX\inventories\actions\RepairItemAction;
use CLADevs\VanillaX\inventories\actions\TradeItemAction;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\InventoryManager;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;

class TypeConverterX extends TypeConverter{

    public const SOURCE_CRAFT_SLOT = 100;

    public const SOURCE_TYPE_ANVIL_INPUT = -10;
    public const SOURCE_TYPE_ANVIL_MATERIAL = -11;

    public const SOURCE_TYPE_ENCHANT_INPUT = -15;
    public const SOURCE_TYPE_ENCHANT_MATERIAL = -16;

    public const SOURCE_TYPE_TRADE_INPUT = -31;
    public const SOURCE_TYPE_TRADE_OUTPUT = -30;

    public function createInventoryAction(NetworkInventoryAction $action, Player $player, InventoryManager $inventoryManager): ?InventoryAction{
        $oldItem = TypeConverter::getInstance()->netItemStackToCore($action->oldItem->getItemStack());
        $newItem = TypeConverter::getInstance()->netItemStackToCore($action->newItem->getItemStack());
        $currentWindowId = $inventoryManager->getCurrentWindowId();
        $currentWindow = $player->getCurrentWindow();

        if($currentWindow instanceof FakeBlockInventory){
            switch($action->sourceType){
                case NetworkInventoryAction::SOURCE_CONTAINER:
                    /** Slot Change Actions */
                    if($action->windowId === ContainerIds::UI){
                        $foundSlot = false;
                        $anvilSlot = UIInventorySlotOffset::ANVIL[$action->inventorySlot] ?? null;
                        $enchantingTableSlot = UIInventorySlotOffset::ENCHANTING_TABLE[$action->inventorySlot] ?? null;
                        $tradeSlot = UIInventorySlotOffset::TRADE2_INGREDIENT[$action->inventorySlot] ?? null;

                        switch($currentWindow->getNetworkType()){
                            case WindowTypes::ANVIL:
                                if($anvilSlot !== null){
                                    $action->inventorySlot = $anvilSlot;
                                    $foundSlot = true;
                                }
                                break;
                            case WindowTypes::ENCHANTMENT:
                                if($enchantingTableSlot !== null){
                                    $action->inventorySlot = $enchantingTableSlot;
                                    $foundSlot = true;
                                }
                                break;
                            case WindowTypes::TRADING:
                                if($tradeSlot !== null){
                                    $action->inventorySlot = $tradeSlot;
                                    $foundSlot = true;
                                }
                                break;
                        }
                        if($foundSlot){
                            return new SlotChangeAction($currentWindow, $action->inventorySlot, $oldItem, $newItem);
                        }
                    }
                    break;
                case NetworkInventoryAction::SOURCE_TODO:
                case self::SOURCE_CRAFT_SLOT:
                    /** Results */
                    switch($action->windowId){
                        case NetworkInventoryAction::SOURCE_TYPE_ANVIL_OUTPUT:
                        case NetworkInventoryAction::SOURCE_TYPE_ANVIL_RESULT:
                        case self::SOURCE_TYPE_ANVIL_MATERIAL:
                        case self::SOURCE_TYPE_ANVIL_INPUT:
                            if($action->windowId !== NetworkInventoryAction::SOURCE_TYPE_ANVIL_OUTPUT){
                                return new RepairItemAction($oldItem, $newItem, $action->windowId);
                            }
                            return new SlotChangeAction($currentWindow, $action->inventorySlot, $oldItem, $newItem);
                        case NetworkInventoryAction::SOURCE_TYPE_ENCHANT_OUTPUT:
                        case self::SOURCE_TYPE_ENCHANT_MATERIAL:
                        case self::SOURCE_TYPE_ENCHANT_INPUT:
                            return new EnchantItemAction($oldItem, $newItem, $action->windowId);
                        case self::SOURCE_TYPE_TRADE_INPUT:
                        case self::SOURCE_TYPE_TRADE_OUTPUT:
                            $action->inventorySlot = $action->windowId === self::SOURCE_TYPE_TRADE_OUTPUT ? 1 : 0;
                            $action->windowId = $currentWindowId;
                            return new TradeItemAction($oldItem, $newItem);
                    }
                    break;
            }
        }
        return parent::createInventoryAction($action, $player, $inventoryManager);
    }

}
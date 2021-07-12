<?php

namespace CLADevs\VanillaX\network\protocol\types;

use CLADevs\VanillaX\inventories\actions\EnchantItemAction;
use CLADevs\VanillaX\inventories\actions\RepairItemAction;
use CLADevs\VanillaX\inventories\actions\TradeItemAction;
use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use UnexpectedValueException;

class NetworkInventoryActionX extends NetworkInventoryAction{

    public const SOURCE_CRAFT_SLOT = 100;

    public const SOURCE_TYPE_ANVIL_INPUT = -10;
    public const SOURCE_TYPE_ANVIL_MATERIAL = -11;

    public const SOURCE_TYPE_ENCHANT_INPUT = -15;
    public const SOURCE_TYPE_ENCHANT_MATERIAL = -16;

    public const SOURCE_TYPE_TRADE_INPUT = -31;
    public const SOURCE_TYPE_TRADE_OUTPUT = -30;

    public function createInventoryAction(Player $player): ?InventoryAction{
        /** Nukkit Transaction for Anvil */
        $oldItem = TypeConverter::getInstance()->netItemStackToCore($this->oldItem->getItemStack());
        $newItem = TypeConverter::getInstance()->netItemStackToCore($this->newItem->getItemStack());

        switch($this->sourceType){
            case self::SOURCE_CONTAINER:

                $otherInventory = true;
                if($this->windowId === ContainerIds::UI){
                    if(array_key_exists($this->inventorySlot, UIInventorySlotOffset::ANVIL)){
                        //Anvil
                        $window = $player->getCurrentWindow();

                        if(!$window instanceof AnvilInventory){
                            throw new UnexpectedValueException("Player " . $player->getName() . " has no open anvil window");
                        }
                        $this->windowId = WindowTypes::ANVIL;
                        $this->inventorySlot = UIInventorySlotOffset::ANVIL[$this->inventorySlot];
                        $otherInventory = false;
                    }elseif(array_key_exists($this->inventorySlot, UIInventorySlotOffset::ENCHANTING_TABLE)){
                        //Enchantment Table
                        $window = $player->getCurrentWindow();

                        if(!$window instanceof EnchantInventory){
                            throw new UnexpectedValueException("Player " . $player->getName() . " has no open enchant window");
                        }
                        $this->windowId = WindowTypes::ENCHANTMENT;
                        $this->inventorySlot = UIInventorySlotOffset::ENCHANTING_TABLE[$this->inventorySlot];
                        $otherInventory = false;
                    }elseif(array_key_exists($this->inventorySlot, UIInventorySlotOffset::TRADE2_INGREDIENT)){
                        //Trade
                        $window = $player->getCurrentWindow();

                        if(!$window instanceof TradeInventory){
                            throw new UnexpectedValueException("Player " . $player->getName() . " has no open trade window");
                        }
                        $this->windowId = WindowTypes::TRADING;
                        $this->inventorySlot = UIInventorySlotOffset::TRADE2_INGREDIENT[$this->inventorySlot];
                        $otherInventory = false;
                    }
                }
                if(!$otherInventory && ($window = $player->getNetworkSession()->getInvManager()->getWindow($this->windowId) !== null)){
                    return new SlotChangeAction($player->getCurrentWindow(), $this->inventorySlot, $oldItem, $newItem);
                }
                break;
            case self::SOURCE_TODO:
            case self::SOURCE_CRAFT_SLOT:
                if($this->windowId >= self::SOURCE_TYPE_ANVIL_OUTPUT && $this->windowId <= self::SOURCE_TYPE_ANVIL_INPUT){
                    //Anvil
                    $window = $player->getCurrentWindow();

                    if(!$window instanceof AnvilInventory){
                        throw new UnexpectedValueException("Player " . $player->getName() . " has no open anvil window");
                    }
                    switch($this->windowId){
                        case self::SOURCE_TYPE_ANVIL_INPUT:
                        case self::SOURCE_TYPE_ANVIL_MATERIAL:
                        case self::SOURCE_TYPE_ANVIL_RESULT:
                            return new RepairItemAction($oldItem, $newItem, $this->windowId);
                    }
                    return new SlotChangeAction($window, $this->inventorySlot, $oldItem, $newItem);
                }elseif($this->windowId >= self::SOURCE_TYPE_ENCHANT_OUTPUT && $this->windowId <= self::SOURCE_TYPE_ENCHANT_INPUT){
                    //Enchantment Table
                    $window = $player->getCurrentWindow();

                    if(!$window instanceof EnchantInventory){
                        throw new UnexpectedValueException("Player " . $player->getName() . " has no open enchant window");
                    }
                    switch($this->windowId){
                        case self::SOURCE_TYPE_ENCHANT_INPUT:
                        case self::SOURCE_TYPE_ENCHANT_MATERIAL:
                        case self::SOURCE_TYPE_ENCHANT_OUTPUT:
                            return new EnchantItemAction($oldItem, $newItem, $this->windowId);
                    }
                    return new SlotChangeAction($window, $this->inventorySlot, $oldItem, $newItem);
                }elseif($this->windowId === self::SOURCE_TYPE_TRADE_OUTPUT || $this->windowId === self::SOURCE_TYPE_TRADE_INPUT){
                    //Trade
                    $window = $player->getCurrentWindow();

                    if(!$window instanceof TradeInventory){
                        throw new UnexpectedValueException("Player " . $player->getName() . " has no open trade window");
                    }
                    $this->inventorySlot = $this->windowId === self::SOURCE_TYPE_TRADE_OUTPUT ? 1 : 0;
                    $this->windowId = WindowTypes::TRADING;
                    return new TradeItemAction($oldItem, $newItem);
                }
                break;
        }
        return null;
    }
}
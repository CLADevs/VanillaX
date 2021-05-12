<?php

namespace CLADevs\VanillaX\network\protocol\types;

use CLADevs\VanillaX\inventories\actions\RepairItemAction;
use CLADevs\VanillaX\inventories\AnvilInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use UnexpectedValueException;

class NetworkInventoryActionX extends NetworkInventoryAction{

    public const SOURCE_CRAFT_SLOT = 100;

    public const SOURCE_TYPE_ANVIL_INPUT = -10;
    public const SOURCE_TYPE_ANVIL_MATERIAL = -11;

    public function createInventoryAction(Player $player): ?InventoryAction{
        /** Nukkit Transaction for Anvil */
        if($this->sourceType === self::SOURCE_CONTAINER){
            if($this->windowId == 124 && array_key_exists($this->inventorySlot, UIInventorySlotOffset::ANVIL)){
                $window = $player->getWindow(WindowTypes::ANVIL);

                if(!$window instanceof AnvilInventory){
                    throw new UnexpectedValueException("Player " . $player->getName() . " has no open anvil window");
                }
                $this->windowId = WindowTypes::ANVIL;
                $this->inventorySlot = UIInventorySlotOffset::ANVIL[$this->inventorySlot];
            }
            if(($window = $player->getWindow($this->windowId)) != null){
                return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem->getItemStack(), $this->newItem->getItemStack());
            }
        }elseif($this->sourceType === self::SOURCE_TODO || $this->sourceType === self::SOURCE_CRAFT_SLOT){
            if($this->windowId >= self::SOURCE_TYPE_ANVIL_OUTPUT && $this->windowId <= self::SOURCE_TYPE_ANVIL_INPUT){
                $window = $player->getWindow(WindowTypes::ANVIL);

                if(!$window instanceof AnvilInventory){
                    throw new UnexpectedValueException("Player " . $player->getName() . " has no open anvil window");
                }
                switch($this->windowId){
                    case self::SOURCE_TYPE_ANVIL_INPUT:
                    case self::SOURCE_TYPE_ANVIL_MATERIAL:
                    case self::SOURCE_TYPE_ANVIL_RESULT:
                        return new RepairItemAction($this->oldItem->getItemStack(), $this->newItem->getItemStack(), $this->windowId);
                }
                return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem->getItemStack(), $this->newItem->getItemStack());
            }
        }
        return parent::createInventoryAction($player);
    }
}
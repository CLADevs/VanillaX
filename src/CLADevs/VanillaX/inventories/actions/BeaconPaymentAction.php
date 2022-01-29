<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\event\inventory\BeaconPaymentEvent;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\player\Player;

class BeaconPaymentAction extends InventoryAction{

    public function execute(Player $source): void{
        $inventory = $source->getCurrentWindow();

        if(!$inventory instanceof BeaconInventory){
            throw new TransactionValidationException("Beacon Inventory is not opened");
        }
        $ev = new BeaconPaymentEvent($source, $inventory, $this->targetItem);
        $ev->call();

        if($ev->isCancelled()){
            throw new TransactionValidationException("Failed to finish beacon payment cancelled");
        }
        $tile = $inventory->getHolder()->getWorld()->getTile($inventory->getHolder());

        if(!$tile instanceof BeaconTile){
            throw new TransactionValidationException("Beacon Inventory is not suppose to be opened");
        }
        $tile->addToQueue($source, $this);
    }

    public function validate(Player $source): void{
        $inventory = $source->getCurrentWindow();

        if(!$inventory instanceof BeaconInventory){
            throw new TransactionValidationException("Beacon Inventory is not opened");
        }
        if(!$inventory->getItem(0)->equalsExact($this->targetItem)){
            throw new TransactionValidationException("Beacon target item is not same as given target item");
        }
    }
}
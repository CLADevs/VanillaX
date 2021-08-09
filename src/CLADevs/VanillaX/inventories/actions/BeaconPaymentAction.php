<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\player\Player;

class BeaconPaymentAction extends InventoryAction{

    public function execute(Player $source): void{
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof BeaconInventory){
            throw new TransactionValidationException("Beacon Inventory is not opened");
        }
    }
}
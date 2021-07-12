<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\TradeInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\player\Player;

class TradeItemAction extends InventoryAction{

    public function execute(Player $source): void{
        $inv = $source->getCurrentWindow();

        if($inv instanceof TradeInventory){
            $inv->onTrade($source, $this->sourceItem, $this->targetItem);
        }
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof TradeInventory){
            throw new TransactionValidationException("Trade Inventory is not opened");
        }
    }
}
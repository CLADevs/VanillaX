<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\TradeInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\player\Player;

class TradeItemAction extends InventoryAction{

    public function isValid(Player $source): bool{
        return $source->getWindow(WindowTypes::TRADING) !== null;
    }

    public function execute(Player $source): bool{
        return true;
    }

    public function onExecuteSuccess(Player $source): void{
        $inv = $source->getWindow(WindowTypes::TRADING);

        if($inv instanceof TradeInventory){
            $inv->onTrade($source, $this->sourceItem, $this->targetItem);
        }
    }

    public function onExecuteFail(Player $source): void{
    }
}
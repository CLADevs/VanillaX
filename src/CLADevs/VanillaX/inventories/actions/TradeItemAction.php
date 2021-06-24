<?php

namespace CLADevs\VanillaX\inventories\actions;

use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class TradeItemAction extends InventoryAction{

    public function isValid(Player $source): bool{
        return $source->getWindow(WindowTypes::TRADING) !== null;
    }

    public function execute(Player $source): bool{
        return true;
    }

    public function onExecuteSuccess(Player $source): void{
    }

    public function onExecuteFail(Player $source): void{
    }
}
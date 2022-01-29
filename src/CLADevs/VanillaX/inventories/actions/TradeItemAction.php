<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\player\Player;

class TradeItemAction extends InventoryAction{

    private int $sourceType;

    public function __construct(Item $sourceItem, Item $targetItem, int $sourceType){
        parent::__construct($sourceItem, $targetItem);
        $this->sourceType = $sourceType;
    }

    public function execute(Player $source): void{
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof TradeInventory){
            throw new TransactionValidationException("Trade Inventory is not opened");
        }
    }

    public function isInput(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_TRADE_INPUT;
    }

    public function isResult(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_TRADE_OUTPUT;
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
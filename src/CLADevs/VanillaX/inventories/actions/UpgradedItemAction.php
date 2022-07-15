<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\SmithingInventory;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\player\Player;

class UpgradedItemAction extends InventoryAction{

    private int $sourceType;

    public function __construct(Item $sourceItem, Item $targetItem, int $sourceType){
        parent::__construct($sourceItem, $targetItem);
        $this->sourceType = $sourceType;
    }

    public function execute(Player $source): void{
        $inventory = $source->getCurrentWindow();

        if(!$inventory instanceof SmithingInventory){
            throw new TransactionValidationException("Smithing Inventory is not opened");
        }
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof SmithingInventory){
            throw new TransactionValidationException("Smithing Inventory is not opened");
        }
    }

    public function isInput(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_ANVIL_INPUT;
    }

    public function isMaterial(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_ANVIL_MATERIAL;
    }

    public function isResult(): bool{
        return $this->sourceType === NetworkInventoryAction::SOURCE_TYPE_ANVIL_RESULT;
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
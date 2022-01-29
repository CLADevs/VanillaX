<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\player\Player;

class EnchantItemAction extends InventoryAction{

    private int $sourceType;

    public function __construct(Item $sourceItem, Item $targetItem, int $sourceType){
        parent::__construct($sourceItem, $targetItem);
        $this->sourceType = $sourceType;
    }

    public function execute(Player $source): void{
        $inventory = $source->getCurrentWindow();

        if(!$inventory instanceof EnchantInventory){
            throw new TransactionValidationException("Enchantment Inventory is not opened");
        }
        if($this->isResult()){
            $inventory->onSuccess($source, $this->getSourceItem());
        }
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof EnchantInventory){
            throw new TransactionValidationException("Enchantment Inventory is not opened");
        }
    }

    public function isInput(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_ENCHANT_INPUT;
    }

    public function isMaterial(): bool{
        return $this->sourceType === TypeConverterX::SOURCE_TYPE_ENCHANT_MATERIAL;
    }

    public function isResult(): bool{
        return $this->sourceType === NetworkInventoryAction::SOURCE_TYPE_ENCHANT_OUTPUT;
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
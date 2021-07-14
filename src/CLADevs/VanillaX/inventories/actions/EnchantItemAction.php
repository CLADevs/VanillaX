<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\EnchantInventory;
use pocketmine\block\BlockLegacyIds;
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
        $inv = $source->getCurrentWindow();

        if($inv instanceof EnchantInventory && $this->targetItem->getId() === BlockLegacyIds::AIR && $this->sourceType === NetworkInventoryAction::SOURCE_TYPE_ENCHANT_OUTPUT){
            $inv->onSuccess($source, $this->sourceItem);
        }
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof EnchantInventory){
            throw new TransactionValidationException("Enchantment Inventory is not opened");
        }
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
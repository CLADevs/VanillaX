<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\AnvilInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\player\Player;

class RepairItemAction extends InventoryAction{

    /** Nukkit Repair Item Action */

    private int $sourceType;

    public function __construct(Item $sourceItem, Item $targetItem, int $sourceType){
        parent::__construct($sourceItem, $targetItem);
        $this->sourceType = $sourceType;
    }

    public function execute(Player $source): void{
        $inv = $source->getCurrentWindow();

        if($inv instanceof AnvilInventory && $this->targetItem->getId() === BlockLegacyIds::AIR){
            $inv->onSuccess($source, $this->sourceItem);
        }
    }

    public function validate(Player $source): void{
        if(!$source->getCurrentWindow() instanceof AnvilInventory){
            throw new TransactionValidationException("Anvil Inventory is not opened");
        }
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
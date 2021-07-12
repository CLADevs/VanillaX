<?php

namespace CLADevs\VanillaX\inventories\actions;

use CLADevs\VanillaX\inventories\types\AnvilInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\player\Player;

class RepairItemAction extends InventoryAction{

    /** Nukkit Repair Item Action */

    private int $sourceType;

    public function __construct(Item $sourceItem, Item $targetItem, int $sourceType){
        parent::__construct($sourceItem, $targetItem);
        $this->sourceType = $sourceType;
    }

    public function isValid(Player $source): bool{
        return $source->getWindow(WindowTypes::ANVIL) !== null;
    }

    public function execute(Player $source): bool{
        return true;
    }

    public function onExecuteSuccess(Player $source): void{
        $inv = $source->getWindow(WindowTypes::ANVIL);

        if($inv instanceof AnvilInventory && $this->targetItem->getId() === BlockLegacyIds::AIR){
            $inv->onSuccess($source, $this->sourceItem);
        }
    }

    public function onExecuteFail(Player $source): void{
    }

    public function getType(): int{
        return $this->sourceType;
    }
}
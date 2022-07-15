<?php

namespace CLADevs\VanillaX\inventories\transaction;

use CLADevs\VanillaX\event\inventory\UpgradedItemEvent;
use CLADevs\VanillaX\inventories\actions\UpgradedItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;

class UpgradeTransaction extends InventoryTransaction{

    private ?Item $input = null;
    private ?Item $materialCost = null;
    private ?Item $result = null;

    public function addAction(InventoryAction $action): void{
        if($action instanceof UpgradedItemAction){
            if($action->isInput()){
                $this->input = $action->getTargetItem();
            }elseif($action->isResult()){
                $source = $action->getSourceItem();
                $target = $action->getTargetItem();

                if(!$source->isNull()){
                    $this->result = $source;
                }elseif(!$target->isNull()){
                    $this->materialCost = $target;
                }
            }
        }
        parent::addAction($action);
    }

    public function execute(): void{
        $ev = new UpgradedItemEvent($this->source, $this->input, $this->materialCost, $this->result);
        $ev->call();

        if($ev->isCancelled()){
            throw new TransactionValidationException("cancelled transaction");
        }
        parent::execute();
    }

    public function canExecute(): bool{
        return $this->input !== null && $this->result !== null;
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getMaterialCost(): ?Item{
        return $this->materialCost;
    }

    public function getResult(): ?Item{
        return $this->result;
    }
}
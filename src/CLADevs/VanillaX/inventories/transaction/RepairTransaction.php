<?php

namespace CLADevs\VanillaX\inventories\transaction;

use CLADevs\VanillaX\event\inventory\RepairedItemEvent;
use CLADevs\VanillaX\inventories\actions\RepairItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;

class RepairTransaction extends InventoryTransaction{

    private SlotChangeAction $inputAction;
    private SlotChangeAction $resultAction;
    private ?SlotChangeAction $materialAction = null;

    private ?Item $input = null;
    private ?Item $material = null;
    private ?Item $result = null;

    private int $validActionCount = 0;

    public function addAction(InventoryAction $action): void{
        if($action instanceof RepairItemAction){
            if($action->isInput()){
                $this->input = $action->getTargetItem();
            }elseif($action->isMaterial()){
                $this->material = $action->getTargetItem();
            }elseif($action->isResult()){
                $this->result = $action->getSourceItem();
            }
        }elseif($action instanceof SlotChangeAction){
            $this->validActionCount++;

            switch($this->validActionCount){
                case 1:
                    if($this->material !== null){
                        $this->materialAction = $action;
                    }else{
                        $this->inputAction = $action;
                    }
                    break;
                case 2:
                    if($this->material !== null){
                        $this->inputAction = $action;
                    }else{
                        $this->resultAction = $action;
                    }
                    break;
                case 3:
                    $this->resultAction = $action;
                    break;
            }
        }
        parent::addAction($action);
    }

    public function execute(): void{
        if(!$this->inputAction->getSourceItem()->equalsExact($this->input)){
            throw new TransactionValidationException("Input item does not match");
        }
        if($this->material !== null && !$this->materialAction->getSourceItem()->equals($this->material)){
            throw new TransactionValidationException("Material item does not match");
        }
        $ev = new RepairedItemEvent($this->source, $this->input, $this->material, $this->result);
        $ev->call();
        parent::execute();
    }

    public function canExecute(): bool{
        return $this->input !== null && $this->result !== null;
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getMaterial(): ?Item{
        return $this->material;
    }

    public function getResult(): ?Item{
        return $this->result;
    }

    public function getInputAction(): SlotChangeAction{
        return $this->inputAction;
    }

    public function getMaterialAction(): ?SlotChangeAction{
        return $this->materialAction;
    }

    public function getResultAction(): SlotChangeAction{
        return $this->resultAction;
    }
}
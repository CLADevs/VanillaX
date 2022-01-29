<?php

namespace CLADevs\VanillaX\inventories\transaction;

use CLADevs\VanillaX\event\inventory\EnchantedItemEvent;
use CLADevs\VanillaX\inventories\actions\EnchantItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnchantTransaction extends InventoryTransaction{

    private bool $hasMaterial;

    private ?Item $oldMaterial = null;
    private ?Item $newMaterial = null;
    private ?Item $input = null;
    private ?Item $result = null;

    public function __construct(Player $source, array $actions = []){
        parent::__construct($source, $actions);
        $this->hasMaterial = !$source->isCreative();
    }

    public function addAction(InventoryAction $action): void{
        if($action instanceof EnchantItemAction){
            if($action->isInput()){
                $this->input = $action->getTargetItem();
            }elseif($action->isMaterial()){
                $this->oldMaterial = $action->getTargetItem();
                $this->newMaterial = $action->getSourceItem();
            }elseif($action->isResult()){
                $this->result = $action->getSourceItem();
            }
        }
        parent::addAction($action);
    }

    public function execute(): void{
        $cost = $this->oldMaterial !== null ? ($this->oldMaterial->getCount() - $this->newMaterial->getCount()) : 0;
        $ev = new EnchantedItemEvent($this->source, $this->input, $this->result, $cost);
        $ev->call();
        parent::execute();
    }

    public function canExecute(): bool{
        $hasItems = $this->input !== null && $this->result !== null;

        if($hasItems && $this->hasMaterial && $this->newMaterial === null){
            return false;
        }
        return $hasItems;
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getOldMaterial(): ?Item{
        return $this->oldMaterial;
    }

    public function getNewMaterial(): ?Item{
        return $this->newMaterial;
    }

    public function getResult(): ?Item{
        return $this->result;
    }
}
<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\HopperInventory;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Spawnable;

class HopperTile extends Spawnable implements Container{
use ContainerTrait;

    private int $tick = 20;
    private int $transferCooldown = 0;
    private HopperInventory $inventory;

    public function getName(): string{
        return "Hopper";
    }

    public function getInventory(): HopperInventory{
        return $this->inventory;
    }

    public function getRealInventory(): HopperInventory{
        return $this->inventory;
    }

    public function onUpdate(): bool{
        if($this->tick > 0){
            $this->tick--;
            if($this->transferCooldown > 0) $this->transferCooldown--;
        }else{
            $this->tick = 20;
        }
        //TODO move this on block collision
        foreach($this->getLevel()->getChunkAtPosition($this)->getEntities() as $entity){
            if($entity instanceof ItemEntity){
                if($entity->getY() >= $this->y && $entity->getY() <= $this->y + 1){
                    $this->onItemCollide($entity);
                }
            }
        }
        return true;
    }

    public function onItemCollide(ItemEntity $entity): void{
        $item = $entity->getItem();
        $this->onAddItem($item);
        if($item->getCount() <= 0 && !$entity->isFlaggedForDespawn()) $entity->flagForDespawn();
    }

    public function onAddItem(Item $item): bool{
        $clone = clone $item;
        if($this->inventory->canAddItem($clone->setCount(1))){
            $this->inventory->addItem($clone);
            $item->pop();
            return true;
        }
        return false;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new HopperInventory($this);
        $this->loadItems($nbt);
        $this->scheduleUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
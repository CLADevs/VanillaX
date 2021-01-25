<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\DropperInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Spawnable;

class DropperTile extends Spawnable implements Container{
    use ContainerTrait;

    private DropperInventory $inventory;

    public function getName(): string{
        return "Dropper";
    }

    public function getInventory(): DropperInventory{
        return $this->inventory;
    }

    public function getRealInventory(): DropperInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new DropperInventory($this);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\types\StoneCutterInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Spawnable;

class StoneCutterTile extends Spawnable implements Container{
    use ContainerTrait;

    private StoneCutterInventory $inventory;

    public function getName(): string{
        return "Stonecutter";
    }

    public function getInventory(): StoneCutterInventory{
        return $this->inventory;
    }

    public function getRealInventory(): StoneCutterInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new StoneCutterInventory($this);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
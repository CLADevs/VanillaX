<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\DispenserInventory;
use pocketmine\block\BlockIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Spawnable;

class DispenserTile extends Spawnable implements Container{
    use ContainerTrait;

    const TILE_ID = TileVanilla::DISPENSER;
    const TILE_BLOCK = BlockIds::DISPENSER;

    private DispenserInventory $inventory;

    public function getInventory(): DispenserInventory{
        return $this->inventory;
    }

    public function getRealInventory(): DispenserInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new DispenserInventory($this);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
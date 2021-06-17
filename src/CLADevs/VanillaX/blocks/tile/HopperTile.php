<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\HopperInventory;
use pocketmine\block\BlockIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Spawnable;

class HopperTile extends Spawnable implements Container{
use ContainerTrait;

    const TILE_ID = TileVanilla::HOPPER;
    const TILE_BLOCK = BlockIds::HOPPER_BLOCK;

    private int $tick = 20;
    private int $transferCooldown = 0;

    private HopperInventory $inventory;

    public function getInventory(): HopperInventory{
        return $this->inventory;
    }

    public function getRealInventory(): HopperInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new HopperInventory($this);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
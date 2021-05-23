<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\types\ShulkerBoxInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\Spawnable;

class ShulkerBoxTile extends Spawnable implements Container{
    use ContainerTrait;

    private ShulkerBoxInventory $inventory;

    public function getInventory(): ShulkerBoxInventory{
        return $this->inventory;
    }

    public function getRealInventory(): ShulkerBoxInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new ShulkerBoxInventory($this);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $nbt->setString(Nameable::TAG_CUSTOM_NAME, "Shulker Box");
    }
}
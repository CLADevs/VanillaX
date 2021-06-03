<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\types\ShulkerBoxInventory;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\Spawnable;

class ShulkerBoxTile extends Spawnable{
    use ContainerTrait;

    const TAG_FACING = "facing";

    private ShulkerBoxInventory $inventory;

    private int $facing = 0;

    public function getInventory(): ShulkerBoxInventory{
        return $this->inventory;
    }

    public function getRealInventory(): ShulkerBoxInventory{
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new ShulkerBoxInventory($this);
        $this->loadItems($nbt);
        if($nbt->hasTag(self::TAG_FACING, ByteTag::class)){
            $this->facing = $nbt->getByte(self::TAG_FACING);
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $nbt->setString(Nameable::TAG_CUSTOM_NAME, "Shulker Box");
        $nbt->setByte(self::TAG_FACING, $this->facing);
    }
}
<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Spawnable;

class BeaconTile extends Spawnable{

    const TILE_ID = TileVanilla::BEACON;
    const TILE_BLOCK = BlockIds::BEACON;

    protected function readSaveData(CompoundTag $nbt): void{
    }

    protected function writeSaveData(CompoundTag $nbt): void{
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
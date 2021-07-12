<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;

class BeaconTile extends Spawnable{

    const TAG_PRIMARY = "primary";
    const TAG_SECONDARY = "secondary";

    const TILE_ID = TileVanilla::BEACON;
    const TILE_BLOCK = BlockLegacyIds::BEACON;

    public function readSaveData(CompoundTag $nbt): void{
    }

    protected function writeSaveData(CompoundTag $nbt): void{
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
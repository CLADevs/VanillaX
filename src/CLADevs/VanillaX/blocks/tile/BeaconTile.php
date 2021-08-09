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

    private int $primary = 0;
    private int $secondary = 0;

    public function setPrimary(int $primary): void{
        $this->primary = $primary;
    }

    public function getPrimary(): int{
        return $this->primary;
    }

    public function setSecondary(int $secondary): void{
        $this->secondary = $secondary;
    }

    public function getSecondary(): int{
        return $this->secondary;
    }

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_PRIMARY)) !== null){
            $this->primary = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_SECONDARY)) !== null){
            $this->secondary = $tag->getValue();
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setInt(self::TAG_PRIMARY, $this->primary);
        $nbt->setInt(self::TAG_SECONDARY, $this->secondary);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->writeSaveData($nbt);
    }
}
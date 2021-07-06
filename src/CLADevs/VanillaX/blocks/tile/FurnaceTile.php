<?php

namespace CLADevs\VanillaX\blocks\tile;

use pocketmine\block\BlockIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\tile\Furnace;

class FurnaceTile extends Furnace{

    const TILE_ID = self::FURNACE;
    const TILE_BLOCK = BlockIds::FURNACE;

    private float $xpHolder = 0.0;

    protected function writeSaveData(CompoundTag $nbt): void{
        parent::writeSaveData($nbt);
        $nbt->setFloat("xpHolder", $this->xpHolder);
    }

    protected function readSaveData(CompoundTag $nbt): void{
        parent::readSaveData($nbt);

        if($nbt->hasTag("xpHolder", FloatTag::class)){
            $this->xpHolder = $nbt->getFloat("xpHolder", 0.0);
        }
    }

    public function getXpHolder(): float{
        return $this->xpHolder;
    }

    public function setXpHolder(float $xpHolder): void{
        $this->xpHolder = $xpHolder;
    }
}
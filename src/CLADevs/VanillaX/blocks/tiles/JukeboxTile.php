<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Tile;

class JukeboxTile extends Tile{

    private ?Item $recordItem = null;

    public function insertTrack(MusicDiscItem $disc): bool{
        if($this->recordItem !== null){
            $this->getLevel()->dropItem($this->add(0, 1), $this->recordItem);
        }
        $this->recordItem = $disc;
        $disc->pop();
        return false;
    }

    public function getRecordItem(): ?Item{
        return $this->recordItem;
    }

    public function setRecordItem(?Item $recordItem): void{
        $this->recordItem = $recordItem;
    }

    protected function readSaveData(CompoundTag $nbt): void{
        if($nbt->hasTag($tag = "RecordItem")){
            $this->recordItem = Item::nbtDeserialize($nbt->getCompoundTag("RecordItem"));
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        if($this->recordItem !== null){
            $nbt->setTag($this->recordItem->nbtSerialize(-1, "RecordItem"));
        }
    }
}
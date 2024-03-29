<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\TileIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Tile;
use pocketmine\item\Item;
use pocketmine\item\Record;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class JukeboxTile extends Tile{

    const TILE_ID = TileIds::JUKEBOX;
    const TILE_BLOCK = BlockLegacyIds::JUKEBOX;

    const TAG_RECORD_ITEM = "RecordItem";
    const TAG_RECORD_DURATION = "RecordDuration";

    private ?Item $recordItem = null;

    private bool $finishedPlaying = false;

    private int $recordDuration = 0;
    private int $recordMaxDuration = -1;

    public function insertTrack(Player $inserter, Record $disc): void{
        $this->recordDuration = 0;
        $this->finishedPlaying = false;
        $this->removeTrack();
        $this->recordItem = clone $disc;
        $disc->pop();
        $inserter->sendPopup(TextFormat::LIGHT_PURPLE . "Now playing: " . $disc->getRecordType()->getSoundName());
        $this->broadcastLevelSoundEvent($this->getPosition(), $disc->getRecordType()->getSoundId());
    }

    public function removeTrack(): void{
        if($this->recordItem !== null){
            $this->recordDuration = 0;
            $this->broadcastLevelSoundEvent($this->getPosition(), LevelSoundEvent::STOP_RECORD);
            $this->getPosition()->getWorld()->dropItem($this->getPosition()->add(0, 1, 0), $this->recordItem);
            $this->recordItem = null;
        }
    }

    public function validateDuration(): void{
        if($this->recordDuration >= $this->recordMaxDuration){
            $this->finishedPlaying = true;
        }
    }

    public function close(): void{
        $this->removeTrack();
        parent::close();
    }

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_RECORD_ITEM)) !== null && $tag instanceof CompoundTag){
            $this->recordItem = Item::nbtDeserialize($tag);
        }
        if(($tag = $nbt->getTag(self::TAG_RECORD_DURATION)) !== null){
            $this->recordDuration = $tag->getValue();
        }
        $this->validateDuration();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        if($this->recordItem !== null){
            $nbt->setTag(self::TAG_RECORD_ITEM, $this->recordItem->nbtSerialize());
        }
        $nbt->setInt(self::TAG_RECORD_DURATION, $this->recordDuration);
    }

    private function broadcastLevelSoundEvent(Position $pos, int $soundId): void{
        $pos->getWorld()->broadcastPacketToViewers($pos, LevelSoundEventPacket::nonActorSound($soundId, $pos, false));
    }

    public function getRecordItem(): ?Item{
        return $this->recordItem;
    }

    public function setRecordItem(?Item $recordItem): void{
        $this->recordItem = $recordItem;
    }

    public function getRecordDuration(): int{
        return $this->recordDuration;
    }

    public function increaseRecordDuration(): void{
        $this->recordDuration++;
    }

    public function getRecordMaxDuration(): int{
        return $this->recordMaxDuration;
    }

    public function setRecordMaxDuration(int $recordMaxDuration): void{
        $this->recordMaxDuration = $recordMaxDuration;
    }

    public function isFinishedPlaying(): bool{
        return $this->finishedPlaying;
    }
}
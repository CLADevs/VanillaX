<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class JukeboxTile extends Tile{

    const TILE_ID = TileVanilla::JUKEBOX;
    const TILE_BLOCK = BlockLegacyIds::JUKEBOX;

    const TAG_RECORD_ITEM = "RecordItem";
    const TAG_RECORD_DURATION = "RecordDuration";

    private ?Item $recordItem = null;

    private bool $finishedPlaying = false;

    private int $recordDuration = 0;
    private int $recordMaxDuration = -1;

    public function getRecordItem(): ?Item{
        return $this->recordItem;
    }

    public function setRecordItem(?Item $recordItem): void{
        $this->recordItem = $recordItem;
    }

    public function insertTrack(Player $inserter, MusicDiscItem $disc): void{
        $this->recordDuration = 0;
        $this->finishedPlaying = false;
        $this->removeTrack();
        $this->recordItem = clone $disc;
        $disc->pop();
        $inserter->sendPopup(TextFormat::LIGHT_PURPLE . "Now playing: " . MusicDiscItem::getRecordName($disc->getId()));
        $this->getLevel()->broadcastLevelSoundEvent($this, $disc->getSoundId());
    }

    public function removeTrack(): void{
        if($this->recordItem !== null){
            $this->recordDuration = 0;
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_STOP_RECORD);
            $this->getLevel()->dropItem($this->add(0, 1), $this->recordItem);
            $this->recordItem = null;
        }
    }

    public function validateDuration(): void{
        if($this->recordDuration >= $this->recordMaxDuration){
            $this->finishedPlaying = true;
        }
    }

    public function onUpdate(): bool{
        if($this->recordItem !== null && !$this->finishedPlaying){
            if($this->recordMaxDuration === -1){
                $this->recordMaxDuration = MusicDiscItem::getRecordLength($this->recordItem->getId()) * 20;
            }
            $this->recordDuration++;
            $this->validateDuration();

            if($this->finishedPlaying){
                return true;
            }
            if(Server::getInstance()->getTick() % 30 === 0){
                $this->getLevel()->addParticle(new GenericParticle($this->add(0.5, 1.25, 0.5), Particle::TYPE_NOTE));
            }
        }
        return true;
    }

    public function close(): void{
        $this->removeTrack();
        parent::close();
    }

    protected function readSaveData(CompoundTag $nbt): void{
        if($nbt->hasTag(self::TAG_RECORD_ITEM)){
            $this->recordItem = Item::nbtDeserialize($nbt->getCompoundTag(self::TAG_RECORD_ITEM));
        }
        if($nbt->hasTag(self::TAG_RECORD_DURATION)){
            $this->recordDuration = $nbt->getInt(self::TAG_RECORD_DURATION);
        }
        $this->validateDuration();
        $this->scheduleUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        if($this->recordItem !== null){
            $nbt->setTag($this->recordItem->nbtSerialize(-1, self::TAG_RECORD_ITEM));
        }
        $nbt->setInt(self::TAG_RECORD_DURATION, $this->recordDuration);
    }
}
<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class JukeboxTile extends Tile{

    const TILE_ID = TileVanilla::JUKEBOX;
    const TILE_BLOCK = BlockIds::JUKEBOX;

    const TAG_RECORD_ITEM = "RecordItem";

    private ?Item $recordItem = null;

    public function getRecordItem(): ?Item{
        return $this->recordItem;
    }

    public function setRecordItem(?Item $recordItem): void{
        $this->recordItem = $recordItem;
    }

    public function insertTrack(Player $inserter, MusicDiscItem $disc): void{
        $this->removeTrack();
        $this->recordItem = clone $disc;
        $disc->pop();
        $inserter->sendPopup(TextFormat::LIGHT_PURPLE . "Now playing: " . MusicDiscItem::getRecordName($disc->getId()));
        $this->getLevel()->broadcastLevelSoundEvent($this, $disc->getSoundId());
    }

    public function removeTrack(): void{
        if($this->recordItem !== null){
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_STOP_RECORD);
            $this->getLevel()->dropItem($this->add(0, 1), $this->recordItem);
            $this->recordItem = null;
        }
    }

    public function onUpdate(): bool{
        if($this->recordItem !== null && Server::getInstance()->getTick() % 30 === 0){
            $this->getLevel()->addParticle(new GenericParticle($this->add(0.5, 1.25, 0.5), Particle::TYPE_CARROT));
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
        $this->scheduleUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        if($this->recordItem !== null){
            $nbt->setTag($this->recordItem->nbtSerialize(-1, self::TAG_RECORD_ITEM));
        }
    }
}
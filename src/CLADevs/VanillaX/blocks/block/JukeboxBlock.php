<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\JukeboxTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Record;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\player\Player;
use pocketmine\Server;

class JukeboxBlock extends Opaque{

    const RECORD_PIGSTEP = 759;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::JUKEBOX, 0, null, JukeboxTile::class), "Jukebox", new BlockBreakInfo(2, BlockToolType::AXE, 0, 6));
    }

    public function getFlameEncouragement(): int{
        return 5;
    }

    public function getFlammability(): int{
        return 10;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $tile = $player->getWorld()->getTile($this->getPosition());

        if($player instanceof Player && $tile instanceof JukeboxTile){
            if($item instanceof Record && $tile->getRecordItem() === null){
                $tile->insertTrack($player, $item);
            }else{
                $tile->removeTrack();
            }
        }
        return true;
    }
    
    public function onScheduledUpdate(): void{
        $tile = $this->position->getWorld()->getTile($this->position);

        if(!$tile instanceof JukeboxTile || $tile->isClosed()){
            return;
        }
        if($tile->getRecordItem() !== null && !$tile->isFinishedPlaying()){
            if($tile->getRecordMaxDuration() === -1){
                $tile->setRecordMaxDuration(self::getRecordLength($tile->getRecordItem()->getId()) * 20);
            }
            $tile->increaseRecordDuration();
            $tile->validateDuration();

            if(!$tile->isFinishedPlaying()){
                if(Server::getInstance()->getTick() % 30 === 0){
                    $position = $this->position->add(0.5, 1.25, 0.5);
                    $this->position->getWorld()->broadcastPacketToViewers($position, LevelEventPacket::standardParticle(ParticleIds::NOTE, 0, $position));
                }
            }
        }
        $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public static function getRecordLength(int $id): int{
        return match ($id){
            ItemIds::RECORD_CHIRP, ItemIds::RECORD_CAT => (60 * 3) + 5,
            ItemIds::RECORD_BLOCKS => (60 * 5) + 45,
            ItemIds::RECORD_FAR => (60 * 2) + 54,
            ItemIds::RECORD_MALL => (60 * 3) + 17,
            ItemIds::RECORD_MELLOHI => 60 + 36,
            ItemIds::RECORD_STAL => (60 * 2) + 30,
            ItemIds::RECORD_STRAD => (60 * 3) + 8,
            ItemIds::RECORD_WARD => (60 * 4) + 11,
            ItemIds::RECORD_11 => 60 + 11,
            ItemIds::RECORD_WAIT => (60 * 3) + 51,
            self::RECORD_PIGSTEP => (60 * 2) + 28,
            default => (60 * 2) + 58,
        };
    }
}
<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\JukeboxTile;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\player\Player;
use pocketmine\Server;

class JukeboxBlock extends Opaque{

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
        $tile = $player->getWorld()->getTile($this->getPos());

        if($player !== null && $tile instanceof JukeboxTile){
            if($item instanceof MusicDiscItem && $tile->getRecordItem() === null){
                $tile->insertTrack($player, $item);
            }else{
                $tile->removeTrack();
            }
        }
        return true;
    }
    
    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

        if($tile->isClosed() || !$tile instanceof JukeboxTile){
            return;
        }
        if($tile->getRecordItem() !== null && !$tile->isFinishedPlaying()){
            if($tile->getRecordMaxDuration() === -1){
                $tile->setRecordMaxDuration(MusicDiscItem::getRecordLength($tile->getRecordItem()->getId()) * 20);
            }
            $tile->increaseRecordDuration();
            $tile->validateDuration();

            if(!$tile->isFinishedPlaying()){
                if(Server::getInstance()->getTick() % 30 === 0){
                    $pk = new LevelEventPacket();
                    $pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | ParticleIds::NOTE;
                    $pk->position = $this->pos->add(0.5, 1.25, 0.5);
                    $pk->data = 0;
                    $this->pos->getWorld()->broadcastPacketToViewers($pk->position, $pk);
                }
            }
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
    }
}
<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;

class LightningBoltEntity extends Entity{

    const NETWORK_ID = self::LIGHTNING_BOLT;

    public $width = 1;
    public $height = 1;

    private int $age = 5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setCanSaveWithChunk(false);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);

        //TODO Disgusting code, ill patch it later.
        if($this->age === 1){
            if(in_array(Server::getInstance()->getDifficulty(), [Level::DIFFICULTY_NORMAL, Level::DIFFICULTY_HARD])){
                for($i = 1; $i <= 3; $i++){
                    $block = $this->add(mt_rand(0, 2), mt_rand(0, 2), mt_rand(0, 2));
                    if($this->getLevel()->getBlock($block)->getId() === BlockLegacyIds::AIR && $this->getLevel()->getBlock($block->subtract(0, 1))->getId() !== BlockLegacyIds::AIR){
                        $this->getLevel()->setBlock($block, BlockFactory::get(BlockLegacyIds::FIRE), true, true);
                    }
                }
            }
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_EXPLODE);
        }else{
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_THUNDER);
        }
        --$this->age;
        if($this->age < 1){
            $this->flagForDespawn();
        }
        return $parent;
    }
}
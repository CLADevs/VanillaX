<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\World;

class LightningBoltEntity extends Entity{

    const NETWORK_ID = EntityIds::LIGHTNING_BOLT;

    public float $width = 1;
    public float $height = 1;

    private int $age = 5;

    public function canSaveWithChunk(): bool{
        return false;
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);

        //TODO Disgusting code, ill patch it later.
        if($this->age === 1){
            if(in_array(Server::getInstance()->getDifficulty(), [World::DIFFICULTY_NORMAL, World::DIFFICULTY_HARD])){
                for($i = 1; $i <= 3; $i++){
                    $block = $this->getPosition()->add(mt_rand(0, 2), mt_rand(0, 2), mt_rand(0, 2));
                    if($this->getWorld()->getBlock($block)->getId() === BlockLegacyIds::AIR && $this->getWorld()->getBlock($block->subtract(0, 1, 0))->getId() !== BlockLegacyIds::AIR){
                        $this->getWorld()->setBlock($block, BlockFactory::getInstance()->get(BlockLegacyIds::FIRE, 0));
                    }
                }
            }
            $this->getWorld()->addSound($this->getPosition(), new ExplodeSound());
        }else{
            $this->getWorld()->broadcastPacketToViewers($this->getPosition(), LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_THUNDER, $this->getPosition()));
        }
        --$this->age;
        if($this->age < 1){
            $this->flagForDespawn();
        }
        return $parent;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
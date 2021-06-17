<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;

class FireworkRocketEntity extends Projectile{

    public $width = 0.25;
    public $height = 0.25;

    const NETWORK_ID = self::FIREWORKS_ROCKET;

    private int $age;

    private bool $straight = true;

    public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
        $this->age = 20 + mt_rand(0, 1);
    }

    public function setStraight(bool $straight): void{
        $this->straight = $straight;
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);
        if($this->age > 0){
            $this->age--;
            if($this->straight){
                $this->motion->y += 0.05;
            }
            //TODO Horizontal Firework
        }else{
            if(!$this->isClosed() && !$this->isFlaggedForDespawn()){
                $this->broadcastEntityEvent(ActorEventPacket::FIREWORK_PARTICLES);
                $this->flagForDespawn();
            }
        }
        return $parent;
    }
}
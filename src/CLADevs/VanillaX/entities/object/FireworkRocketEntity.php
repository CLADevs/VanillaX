<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class FireworkRocketEntity extends Projectile{

    public $width = 0.25;
    public $height = 0.25;

    const NETWORK_ID = self::FIREWORKS_ROCKET;

    private int $age = 25;

    public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
        $this->age += mt_rand(0, 7);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);
        if($this->age > 0){
            $this->age--;
            $this->motion->y += 0.1;
        }else{
            if(!$this->isClosed() && !$this->isFlaggedForDespawn()){
                $this->flagForDespawn();
            }
        }
        return $parent;
    }
}
<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Position;

class EnderCrystalEntity extends Entity{

    public $width = 0.98;
    public $height = 0.98;

    protected $gravity = 0.00;

    const NETWORK_ID = self::ENDER_CRYSTAL;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(1);
    }

    public function attack(EntityDamageEvent $source): void{
        if(!$source->isCancelled() && !$this->isClosed() && !$this->isFlaggedForDespawn()){
            $exp = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), 6, $this);
            $exp->explodeA();
            $exp->explodeB();
            $this->flagForDespawn();
        }
    }
}
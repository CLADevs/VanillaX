<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\Explosion;

class EnderCrystalEntity extends Entity{

    public float $width = 0.98;
    public float $height = 0.98;

    /** @var float */
    protected $gravity = 0.00;

    const NETWORK_ID = EntityIds::ENDER_CRYSTAL;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(1);
    }

    public function attack(EntityDamageEvent $source): void{
        if(!$source->isCancelled() && !$this->isClosed() && !$this->isFlaggedForDespawn()){
            $pos = $this->getPosition();
            $pos->y += $this->height / 2;
            $exp = new Explosion($pos, 6, $this);
            $exp->explodeA();
            $exp->explodeB();
            $this->flagForDespawn();
        }
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
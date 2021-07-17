<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VindicatorEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::VINDICATOR;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Vindicator";
    }

    //TODO drops
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? ($this->isBaby() ? 12 : 5) + (mt_rand(1, 3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ILLAGERS;
    }
}
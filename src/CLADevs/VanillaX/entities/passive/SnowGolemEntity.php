<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SnowGolemEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SNOW_GOLEM;

    public float $width = 0.4;
    public float $height = 1.8;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Snow Golem";
    }
}
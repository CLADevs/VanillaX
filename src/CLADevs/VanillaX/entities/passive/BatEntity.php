<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BatEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::BAT;

    public float $width = 0.5;
    public float $height = 0.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Bat";
    }
}
<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DonkeyEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::DONKEY;

    public float $width = 1.4;
    public float $height = 1.6;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Donkey";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
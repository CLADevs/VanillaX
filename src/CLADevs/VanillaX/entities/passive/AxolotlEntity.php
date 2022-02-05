<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;

class AxolotlEntity extends VanillaEntity{

    const NETWORK_ID = "minecraft:axolotl";

    public float $width = 1.3;
    public float $height = 0.6;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(14);
    }

    public function getName(): string{
        return "Axolotl";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}
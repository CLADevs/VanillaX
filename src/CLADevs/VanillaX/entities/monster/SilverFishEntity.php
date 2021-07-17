<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SilverfishEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SILVERFISH;

    public float $width = 0.4;
    public float $height = 0.3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Silverfish";
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}
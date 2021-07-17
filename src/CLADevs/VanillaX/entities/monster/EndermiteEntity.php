<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermiteEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ENDERMITE;

    public float $width = 0.4;
    public float $height = 0.3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Endermite";
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 3 : 0;
    }
}
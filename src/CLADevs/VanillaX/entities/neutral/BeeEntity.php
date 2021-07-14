<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;

class BeeEntity extends VanillaEntity{

    const NETWORK_ID = self::LEGACY_ID_MAP_BC[self::BEE];

    public $width = 0.55;
    public $height = 0.5;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Bee";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
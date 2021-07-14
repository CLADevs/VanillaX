<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;

class WanderingTraderEntity extends VanillaEntity{

    const NETWORK_ID = self::LEGACY_ID_MAP_BC[self::WANDERING_TRADER];

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Wandering Trader";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EnderDragonEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ENDER_DRAGON;

    public float $width = 13;
    public float $height = 4;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(200);
    }

    public function getName(): string{
        return "Ender Dragon";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class OcelotEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::OCELOT;

    public $width = 0.6;
    public $height = 0.7;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Ocelot";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
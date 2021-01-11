<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;

class DolphinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.6;

    const NETWORK_ID = self::DOLPHIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.585, 0.39], [0.9, 0.6]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->ageable->setGrowthItems([ItemIds::FISH, ItemIds::SALMON]);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Dolphin";
    }
}
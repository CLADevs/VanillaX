<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;

class FoxEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = self::FOX;

    protected function initEntity(): void{
        parent::initEntity();
        //TODO find baby fox width, width
        $this->ageable = new EntityAgeable($this, [0.6, 0.7], [0.6, 0.7]);
        $this->ageable->setGrowthItems([ItemIds::SWEET_BERRIES]);
    }

    public function getName(): string{
        return "Fox";
    }
}
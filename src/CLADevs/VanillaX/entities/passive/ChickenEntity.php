<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;

class ChickenEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.8;

    const NETWORK_ID = self::CHICKEN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.4], [0.6, 0.8]);
        $this->ageable->setGrowthItems([ItemIds::WHEAT_SEEDS, ItemIds::BEETROOT_SEEDS, ItemIds::MELON_SEEDS, ItemIds::PUMPKIN_SEEDS]);
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Chicken";
    }
}
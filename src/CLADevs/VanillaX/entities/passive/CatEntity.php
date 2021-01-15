<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;
use pocketmine\item\ItemIds;

class CatEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = self::CAT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.24, 0.28], [0.48, 0.56]);
        $this->ageable->setGrowthItems([ItemIds::FISH, ItemIds::SALMON]);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Cat";
    }
}
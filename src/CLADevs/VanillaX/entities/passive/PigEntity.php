<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;
use pocketmine\item\ItemIds;

class PigEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::PIG;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.45, 0.45], [0.9, 0.9]);
        $this->ageable->setGrowthItems([ItemIds::CARROT, ItemIds::BEETROOT, ItemIds::POTATO]);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Pig";
    }
}
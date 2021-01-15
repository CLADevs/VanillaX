<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;
use pocketmine\item\ItemIds;

class RabbitEntity extends LivingEntity{

    public $width = 0.67;
    public $height = 0.67;

    const NETWORK_ID = self::RABBIT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.268, 0.268], [0.402, 0.402]);
        $this->ageable->setGrowthItems([ItemIds::GOLDEN_CARROT, ItemIds::CARROT, ItemIds::YELLOW_FLOWER]);
        $this->setMaxHealth(3);
    }

    public function getName(): string{
        return "Rabbit";
    }
}
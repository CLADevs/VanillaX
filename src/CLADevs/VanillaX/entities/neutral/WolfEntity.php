<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;
use pocketmine\item\ItemIds;

class WolfEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.85;

    const NETWORK_ID = self::WOLF;

    protected function initEntity(): void{
        parent::initEntity();
        //TODO wild wolf has 8 health and tamed one has 20
        $this->ageable = new EntityAgeable($this, [0.3, 0.4], [0.6, 0.85]);
        $this->ageable->setGrowthItems([
            ItemIds::CHICKEN,
            ItemIds::COOKED_CHICKEN,
            ItemIds::BEEF,
            ItemIds::COOKED_BEEF,
            ItemIds::MUTTONRAW,
            ItemIds::MUTTON_COOKED,
            ItemIds::PORKCHOP,
            ItemIds::COOKED_PORKCHOP,
            ItemIds::RABBIT,
            ItemIds::COOKED_RABBIT,
            ItemIds::ROTTEN_FLESH
        ]);
    }

    public function getName(): string{
        return "Wolf";
    }
}
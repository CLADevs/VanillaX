<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;

class HoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::HOGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.85, 0.86], [0.9, 0.9]);
        //TODO add Crimson Fungus Item
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Hoglin";
    }
}
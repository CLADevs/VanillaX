<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class ShulkerEntity extends LivingEntity{

    public $width = 1;
    public $height = 1;

    const NETWORK_ID = self::SHEEP;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Shulker";
    }
}
<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;

class LlamaEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 1.87;

    const NETWORK_ID = self::LLAMA;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Llama";
    }
}
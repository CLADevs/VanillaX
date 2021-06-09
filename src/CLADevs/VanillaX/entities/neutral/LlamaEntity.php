<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class LlamaEntity extends VanillaEntity{

    const NETWORK_ID = self::LLAMA;

    public $width = 0.9;
    public $height = 1.87;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Llama";
    }
}
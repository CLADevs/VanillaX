<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class WolfEntity extends VanillaEntity{

    const NETWORK_ID = self::WOLF;

    public $width = 0.6;
    public $height = 0.8;

    public bool $isWild = true;

    protected function initEntity(): void{
        parent::initEntity();
        $this->initializeHealth();
    }

    protected function initializeHealth(): void{
        if($this->isWild){
            $health = 8;
        }else{
            $health = 20;
        }
        $this->setMaxHealth($health);
    }

    public function getName(): string{
        return "Wolf";
    }
}
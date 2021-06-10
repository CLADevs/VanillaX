<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class CatEntity extends VanillaEntity{

    const NETWORK_ID = self::CAT;

    public $width = 0.6;
    public $height = 0.7;
    
    public bool $isWild = true;

    protected function initEntity(): void{
        parent::initEntity();
        $this->initializeHealth();
    }

    protected function initializeHealth(): void{
        if($this->isWild){
            $health = 10;
        }else{
            $health = 20;
        }
        $this->setMaxHealth($health);
    }

    public function getName(): string{
        return "Cat";
    }
}
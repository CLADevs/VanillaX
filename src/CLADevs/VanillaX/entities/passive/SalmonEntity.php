<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class SalmonEntity extends LivingEntity{

    const TYPE_SMALL = 0;
    const TYPE_MEDIUM = 1;
    const TYPE_LARGE = 2;
    
    public $width = 0.5;
    public $height = 0.5;

    const NETWORK_ID = self::SALMON;

    private int $type = self::TYPE_MEDIUM;
    
    protected function initEntity(): void{
        parent::initEntity();
        //TODO
        $this->recalculateScale();
        $this->setMaxHealth(6);
    }
    
    public function recalculateScale(): void{
        switch($this->type){
            case self::TYPE_SMALL:
                $this->width = 0.25;
                $this->height = 0.25;
                break;
            case self::TYPE_MEDIUM:
                $this->width = 0.5;
                $this->height = 0.5;
                break;
            case self::TYPE_LARGE:
                $this->width = 0.75;
                $this->height = 0.75;
                break;
        }
    }

    public function getName(): string{
        return "Salmon";
    }
}
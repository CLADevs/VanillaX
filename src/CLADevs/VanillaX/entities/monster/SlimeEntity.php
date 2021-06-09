<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class SlimeEntity extends VanillaEntity{

    const TYPE_LARGE = 0;
    const TYPE_MEDIUM = 1;
    const TYPE_SMALL = 2;

    const NETWORK_ID = self::SLIME;

    public $width = 2.08;
    public $height = 2.08;

    public int $type = self::TYPE_LARGE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->initializeType($this->type);
    }

    protected function initializeType(int $type): void{
        $health = 16;
        $size = 2.08;

        if($type === self::TYPE_MEDIUM){
            $health = 4;
            $size = 0.78;
        }elseif($type === self::TYPE_SMALL){
            $health = 1;
            $size = 0.52;
        }
        $this->width = $size;
        $this->height = $size;
        $this->recalculateBoundingBox();
        $this->setMaxHealth($health);
        $this->setHealth($health);
    }

    public function getName(): string{
        return "Slime";
    }
}
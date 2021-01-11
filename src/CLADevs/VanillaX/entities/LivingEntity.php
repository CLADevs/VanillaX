<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\entity\Living;

abstract class LivingEntity extends Living{

    protected ?EntityAgeable $ageable = null;

    public function getAgeable(): ?EntityAgeable{
        return $this->ageable;
    }

    public function recalculateBoundingBox(): void{
        parent::recalculateBoundingBox();
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        if($this->isClosed()){
            return false;
        }

        $parent = parent::entityBaseTick($tickDiff);
        if($this->ageable !== null){
            $this->ageable->tick();
        }
        return $parent;
    }
}
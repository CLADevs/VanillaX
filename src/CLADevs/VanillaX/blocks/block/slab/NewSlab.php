<?php

namespace CLADevs\VanillaX\blocks\block\slab;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Slab;
use pocketmine\block\utils\SlabType;

class NewSlab extends Slab implements NonAutomaticCallItemTrait{

    const SLAB_FLAG_UPPER = 1;

    protected function writeStateToMeta(): int{
        if(!$this->slabType->equals(SlabType::DOUBLE())){
            return ($this->slabType->equals(SlabType::TOP()) ? self::SLAB_FLAG_UPPER : 0);
        }
        return 0;
    }

    public function readStateFromData(int $id, int $stateMeta): void{
        if($id === $this->idInfoFlattened->getSecondId()){
            $this->slabType = SlabType::DOUBLE();
        }else{
            $this->slabType = ($stateMeta & self::SLAB_FLAG_UPPER) !== 0 ? SlabType::TOP() : SlabType::BOTTOM();
        }
    }

    public function getStateBitmask(): int{
        return 1;
    }
}
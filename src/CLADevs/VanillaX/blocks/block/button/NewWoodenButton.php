<?php

namespace CLADevs\VanillaX\blocks\block\button;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\WoodenButton;

class NewWoodenButton extends WoodenButton implements NonAutomaticCallItemTrait{

    const BUTTON_FLAG_POWERED = 0x6;

    public function readStateFromData(int $id, int $stateMeta): void{
        $this->facing = $stateMeta;
        $this->pressed = $stateMeta >= self::BUTTON_FLAG_POWERED;
    }

    protected function writeStateToMeta(): int{
        $state = $this->facing;

        if($this->facing >= self::BUTTON_FLAG_POWERED){
            if(!$this->pressed){
                $state -= self::BUTTON_FLAG_POWERED;
            }
        }elseif($this->pressed){
            $state += self::BUTTON_FLAG_POWERED;
        }
        return $state;
    }
}
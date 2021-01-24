<?php

namespace CLADevs\VanillaX\blocks\types;

use pocketmine\block\BlockIds;
use pocketmine\block\Solid;

class JukeboxBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::JUKEBOX, $meta);
    }

    public function getHardness(): float{
        return 2;
    }

    public function getBlastResistance(): float{
        return 6;
    }

    public function getFlameEncouragement(): int{
        return 5;
    }

    public function getFlammability(): int{
        return 10;
    }
}
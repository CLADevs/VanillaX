<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\BlockIdentifiers;
use pocketmine\block\Transparent;

class BubbleColumnBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIdentifiers::BUBBLE_COLUMN, $meta, "Bubble Column");
    }

    public function getBlastResistance(): float{
        return 100;
    }

    public function getHardness(): float{
        return 0;
    }
}
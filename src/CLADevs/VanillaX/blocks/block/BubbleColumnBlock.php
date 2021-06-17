<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use pocketmine\block\Transparent;

class BubbleColumnBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(BlockVanilla::BUBBLE_COLUMN, $meta, "Bubble Column");
    }

    public function getBlastResistance(): float{
        return 100;
    }

    public function getHardness(): float{
        return 0;
    }
}
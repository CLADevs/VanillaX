<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use pocketmine\block\BlockIds;
use pocketmine\block\Transparent;

class ObserverBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::OBSERVER, $meta);
    }

    public function getHardness(): float{
        return 3.5;
    }
}
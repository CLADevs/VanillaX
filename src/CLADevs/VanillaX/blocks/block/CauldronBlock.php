<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\Transparent;
use pocketmine\item\ItemIds;

class CauldronBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::CAULDRON_BLOCK, $meta, "Cauldron", ItemIds::CAULDRON);
    }

    public function getHardness(): float{
        return 2;
    }
}
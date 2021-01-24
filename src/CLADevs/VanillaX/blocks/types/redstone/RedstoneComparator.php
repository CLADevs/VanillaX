<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\Transparent;

class RedstoneComparator extends Transparent implements NonAutomaticCallItemTrait{

    private bool $powered;

    public function __construct(bool $powered, int $meta = 0){
        parent::__construct($powered ? self::POWERED_COMPARATOR : self::UNPOWERED_COMPARATOR, $meta);
        $this->powered = $powered;
    }

    public function getHardness(): float{
        return 0;
    }
}
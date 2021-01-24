<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\Transparent;

class RedstoneRepeater extends Transparent implements NonAutomaticCallItemTrait{

    private bool $powered;

    public function __construct(bool $powered, int $meta = 0){
        parent::__construct($powered ? self::POWERED_REPEATER : self::UNPOWERED_REPEATER, $meta);
        $this->powered = $powered;
    }

    public function getHardness(): float{
        return 0;
    }
}
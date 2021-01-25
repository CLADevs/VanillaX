<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\Transparent;
use pocketmine\item\ItemIds;

class RedstoneRepeater extends Transparent implements NonAutomaticCallItemTrait{

    private bool $powered;

    public function __construct(bool $powered, int $meta = 0){
        $id = $powered ? self::POWERED_REPEATER : self::UNPOWERED_REPEATER;
        $name = ($powered ? "Powered" : "Unpowered") . " Repeater";
        parent::__construct($id, $meta, $name, ItemIds::REPEATER);
        $this->powered = $powered;
    }

    public function getHardness(): float{
        return 0;
    }
}
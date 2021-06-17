<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Transparent;
use pocketmine\item\ItemIds;

class RedstoneComparator extends Transparent implements NonAutomaticCallItemTrait{

    private bool $powered;

    public function __construct(bool $powered, int $meta = 0){
        $id = $powered ? self::POWERED_COMPARATOR : self::UNPOWERED_COMPARATOR;
        $name = ($powered ? "Powered" : "Unpowered") . " Comparator";
        parent::__construct($id, $meta, $name, ItemIds::COMPARATOR);
        $this->powered = $powered;
    }

    public function getHardness(): float{
        return 0;
    }
}
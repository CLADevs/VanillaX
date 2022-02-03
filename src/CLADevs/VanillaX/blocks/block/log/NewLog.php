<?php

namespace CLADevs\VanillaX\blocks\block\log;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Log;

class NewLog extends Log implements NonAutomaticCallItemTrait{

    protected function getAxisMetaShift(): int{
        return 0;
    }
}
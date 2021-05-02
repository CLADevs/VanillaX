<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Durable;

class ShieldItem extends Durable{

    public function __construct(int $meta = 0){
        parent::__construct(self::SHIELD, $meta, "Shield");
    }

    public function getMaxDurability(): int{
        return 336;
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Armor;

class ElytraItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(self::ELYTRA, $meta, "Elytra");
    }

    public function getMaxStackSize(): int{
        return 1;
    }

    public function getMaxDurability(): int{
        return 432;
    }
}
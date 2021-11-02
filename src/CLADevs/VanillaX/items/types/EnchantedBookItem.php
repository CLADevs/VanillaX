<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class EnchantedBookItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::ENCHANTED_BOOK, $meta, "Enchanted Book");
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
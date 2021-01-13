<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class BaneofArthropodsEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::BANE_OF_ARTHROPODS, "Bane of Arthropods", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_AXE, 5);
    }

    public function handle(): void{
        //TODO
    }
}
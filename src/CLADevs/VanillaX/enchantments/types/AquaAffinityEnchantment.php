<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class AquaAffinityEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::AQUA_AFFINITY, "Aqua Affinity", self::RARITY_RARE, self::SLOT_HEAD, self::SLOT_NONE, 1);
    }

    public function handle(): void{
        //TODO
    }
}
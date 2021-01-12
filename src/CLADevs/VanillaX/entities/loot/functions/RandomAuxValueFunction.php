<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\Item;

class RandomAuxValueFunction extends LootFunction{

    const NAME = "random_aux_value";

    private int $min;
    private int $max;

    public function __construct(int $min, int $max){
        $this->min = $min;
        $this->max = $max;
    }

    public function apply(Item $item): void{
        $item->setDamage(mt_rand($this->min, $this->max));
    }
}
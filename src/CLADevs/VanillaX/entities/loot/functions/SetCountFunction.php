<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\Item;

class SetCountFunction extends LootFunction{

    const NAME = "set_count";

    private int $min;
    private int $max;

    public function __construct(int $min, int $max){
        $this->min = $min;
        $this->max = $max;
    }

    public function apply(Item $item): void{
        $count = $this->min;
        if($this->max !== 0){
            $count = mt_rand($this->min, $this->max);
        }
        $item->setCount($item->getCount() + $count);
    }
}
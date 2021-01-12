<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\Durable;
use pocketmine\item\Item;

class SetDamageFunction extends LootFunction{

    const NAME = "set_damage";

    private float $min;
    private float $max;

    public function __construct(float $min, float $max){
        $this->min = $min;
        $this->max = $max;
    }

    public function apply(Item $item): void{
        if($item instanceof Durable){
            $max = $item->getMaxDurability();
            $chance = mt_rand($this->min * 10, $this->max * 10) / 10;
            $item->setDamage(min($chance * 100, $max));
        }
    }
}
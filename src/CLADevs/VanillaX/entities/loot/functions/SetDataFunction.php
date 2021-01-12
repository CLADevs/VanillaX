<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\Item;

class SetDataFunction extends LootFunction{

    const NAME = "set_data";

    private int $data;

    public function __construct(int $data){
        $this->data = $data;
    }

    public function apply(Item $item): void{
        $item->setDamage($this->data);
    }
}
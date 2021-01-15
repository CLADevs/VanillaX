<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;

class ExplorationMapFunction extends LootFunction{

    const NAME = "exploration_map";

    private string $destination;

    public function __construct(string $destination){
        $this->destination = $destination;
    }
}
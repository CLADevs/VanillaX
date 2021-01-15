<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;

class SetBannerDetailsFunction extends LootFunction{

    const NAME = "set_banner_details";

    private int $type;

    public function __construct(int $type){
        $this->type = $type;
    }
}
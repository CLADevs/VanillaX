<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class UnemployedProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::UNEMPLOYED, "Unemployed");
    }

    public function hasTrades(): bool{
        return false;
    }
}
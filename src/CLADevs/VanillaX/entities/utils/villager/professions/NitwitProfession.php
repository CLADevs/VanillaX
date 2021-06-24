<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class NitwitProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::NITWIT, "Nitwit");
    }

    public function hasTrades(): bool{
        return false;
    }
}
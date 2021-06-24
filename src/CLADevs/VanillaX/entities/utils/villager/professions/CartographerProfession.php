<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class CartographerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::CARTOGRAPHER, "Cartographer", BlockVanilla::CARTOGRAPHY_TABLE);
    }
}
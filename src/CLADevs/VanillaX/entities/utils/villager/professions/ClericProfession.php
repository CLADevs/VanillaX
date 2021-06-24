<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockIds;

class ClericProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::CLERIC, "Cleric", BlockIds::BREWING_STAND_BLOCK);
    }
}
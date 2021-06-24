<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockIds;

class MasonProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::MASON, "Stone Mason", BlockIds::STONECUTTER);
    }
}
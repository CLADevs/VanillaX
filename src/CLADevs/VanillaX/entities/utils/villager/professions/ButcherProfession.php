<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class ButcherProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::BUTCHER, "Butcher", BlockLegacyIds::SMOKER);
    }
}
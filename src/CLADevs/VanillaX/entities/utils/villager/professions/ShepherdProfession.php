<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class ShepherdProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::SHEPHERD, "Shepherd", BlockLegacyIds::LOOM);
    }
}
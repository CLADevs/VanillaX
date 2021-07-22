<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class FishermanProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::FISHERMAN, "Fisherman", BlockLegacyIds::BARREL);
    }
}
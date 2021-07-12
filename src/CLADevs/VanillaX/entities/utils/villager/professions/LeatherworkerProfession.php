<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class LeatherworkerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::LEATHER_WORKER, "Leather Worker", BlockLegacyIds::CAULDRON_BLOCK);
    }
}
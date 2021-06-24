<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockIds;

class LeatherworkerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::LEATHER_WORKER, "Leather Worker", BlockIds::CAULDRON_BLOCK);
    }
}
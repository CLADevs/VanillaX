<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class ArmorerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::ARMORER, "Armorer", BlockLegacyIds::BLAST_FURNACE);
    }
}
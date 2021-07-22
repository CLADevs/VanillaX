<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class FletcherProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::FLETCHER, "Fletcher", BlockLegacyIds::FLETCHING_TABLE);
    }
}
<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class CartographerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::CARTOGRAPHER, "Cartographer", BlockLegacyIds::CARTOGRAPHY_TABLE);
    }
}
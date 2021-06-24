<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockIds;

class FarmerProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::FARMER, "Farmer", BlockIds::COMPARATOR_BLOCK);
    }
}
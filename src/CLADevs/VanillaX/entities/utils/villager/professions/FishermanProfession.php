<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class FishermanProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::FISHERMAN, "Fisherman", BlockVanilla::BARREL);
    }
}
<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class WeaponsmithProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::WEAPON_SMITH, "Weapon Smith", BlockVanilla::GRIND_STONE);
    }
}
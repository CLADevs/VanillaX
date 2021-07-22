<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class WeaponsmithProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::WEAPON_SMITH, "Weapon Smith", BlockLegacyIds::GRINDSTONE);
    }
}
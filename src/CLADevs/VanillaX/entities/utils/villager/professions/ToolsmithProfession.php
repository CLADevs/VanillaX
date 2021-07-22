<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use pocketmine\block\BlockLegacyIds;

class ToolsmithProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::TOOL_SMITH, "Tool Smith", BlockLegacyIds::SMITHING_TABLE);
    }
}
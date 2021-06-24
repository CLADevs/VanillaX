<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class ToolsmithProfession extends VillagerProfession{

    public function __construct(){
        parent::__construct(self::TOOL_SMITH, "Tool Smith", BlockVanilla::SMITHING_TABLE);
    }
}
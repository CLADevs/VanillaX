<?php

namespace CLADevs\VanillaX\entities\utils;

use pocketmine\Player;

interface EntityMouseHover{

    public function onMouseHover(Player $player): void;
}
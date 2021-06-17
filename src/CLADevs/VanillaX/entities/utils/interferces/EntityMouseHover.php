<?php

namespace CLADevs\VanillaX\entities\utils\interferces;

use pocketmine\Player;

interface EntityMouseHover{

    public function onMouseHover(Player $player): void;
}
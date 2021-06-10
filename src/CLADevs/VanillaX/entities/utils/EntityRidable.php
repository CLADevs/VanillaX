<?php

namespace CLADevs\VanillaX\entities\utils;

use pocketmine\Player;

interface EntityRidable{

    public function onEnterRide(Player $player): void;
    public function onLeftRide(Player $player): void;
}
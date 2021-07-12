<?php

namespace CLADevs\VanillaX\entities\utils\interfaces;

use pocketmine\player\Player;

interface EntityRidable{

    /**
     * @param Player $player
     * Whenever a player rides a entity
     */
    public function onEnterRide(Player $player): void;

    /**
     * @param Player $player
     * Whenever a player stop riding a entity
     */
    public function onLeftRide(Player $player): void;
}
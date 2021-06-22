<?php

namespace CLADevs\VanillaX\entities\utils\interfaces;

use CLADevs\VanillaX\entities\utils\EntityButtonResult;
use pocketmine\Player;

interface EntityInteractButton{

    /**
     * Whenever you hover over an entity this function would be called
     * @param Player $player
     */
    public function onMouseHover(Player $player): void;

    /**
     * This is whenever a button is pressed
     * @param EntityButtonResult $result
     */
    public function onButtonPressed(EntityButtonResult $result): void;
}
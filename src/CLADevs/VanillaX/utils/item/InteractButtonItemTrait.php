<?php

namespace CLADevs\VanillaX\utils\item;

use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use pocketmine\player\Player;

interface InteractButtonItemTrait{

    /**
     * Whenever you hover over an entity this function would be called
     * @param Player $player
     */
    public function onMouseHover(Player $player): void;

    /**
     * This is whenever a button is pressed
     * @param InteractButtonResult $result
     */
    public function onButtonPressed(InteractButtonResult $result): void;
}
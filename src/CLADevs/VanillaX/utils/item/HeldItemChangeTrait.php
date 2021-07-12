<?php

namespace CLADevs\VanillaX\utils\item;

use pocketmine\item\Item;
use pocketmine\player\Player;

interface HeldItemChangeTrait{

    /**
     * @param Player $player
     * @param Item $old
     * @param Item $new
     * Whenever a player changes their held item slot
     */
    public function onSlotChange(Player $player, Item $old, Item $new): void;
}
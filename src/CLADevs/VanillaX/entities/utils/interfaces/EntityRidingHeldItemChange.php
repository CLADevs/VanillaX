<?php

namespace CLADevs\VanillaX\entities\utils\interfaces;

use pocketmine\item\Item;
use pocketmine\Player;

interface EntityRidingHeldItemChange{

    /**
     * @param Player $player
     * @param Item $old
     * @param Item $new
     * Whenever a player changes their held item while riding a entity
     */
    public function onSlotChange(Player $player, Item $old, Item $new): void;
}
<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FishingRodItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::FISHING_ROD, 0), "Fishing Rod");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
        //TODO finishing
        return ItemUseResult::NONE();
    }
}
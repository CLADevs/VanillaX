<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BoatItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::BOAT, 0), "Boat");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
//        $entity = new BoatEntity($player->getLevel(), BoatEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5)));
//        $entity->spawnToAll();
//        if($player->isSurvival() || $player->isAdventure()) $this->pop();
        return parent::onInteractBlock($player, $blockReplace, $blockClicked, $face, $clickVector);
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
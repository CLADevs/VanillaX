<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\BoatEntity;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BoatItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::BOAT, $meta, "Boat");
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        $entity = new BoatEntity($player->getLevel(), BoatEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5)));
        $entity->spawnToAll();
        if($player->isSurvival() || $player->isAdventure()) $this->pop();
        return true;
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
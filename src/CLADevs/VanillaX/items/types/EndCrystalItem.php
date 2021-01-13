<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\EnderCrystalEntity;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class EndCrystalItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::END_CRYSTAL, $meta, "End Crystal");
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        if($blockClicked->getId() === BlockIds::BEDROCK){
            $entity = new EnderCrystalEntity($player->getLevel(), EnderCrystalEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5)));
            $entity->spawnToAll();
            if($player->isSurvival() || $player->isAdventure()) $this->pop();
        }
        return true;
    }
}
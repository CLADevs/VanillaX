<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FireChargeItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIRE_CHARGE, $meta, "Fire Charge");
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        if($blockReplace->getId() === BlockLegacyIds::AIR){
            $player->getLevel()->setBlock($blockReplace, BlockFactory::get(BlockLegacyIds::FIRE), true, true);
            Session::playSound($player, "mob.blaze.shoot");
            if($player->isSurvival() || $player->isAdventure()) $this->pop();
        }
        return true;
    }
}
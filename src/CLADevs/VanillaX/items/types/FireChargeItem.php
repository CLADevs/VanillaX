<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\session\Session;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FireChargeItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::FIRE_CHARGE, 0), "Fire Charge");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
        if($blockReplace instanceof Air){
            $blockReplace->getPosition()->getWorld()->setBlock($blockReplace->getPosition(), VanillaBlocks::FIRE(), true);
            if($player->hasFiniteResources()) $this->pop();
            Session::playSound($player, "mob.blaze.shoot");
            return ItemUseResult::SUCCESS();
        }
        return ItemUseResult::FAIL();
    }
}
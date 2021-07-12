<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class DragonEggBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::DRAGON_EGG, 0), "Dragon Egg", new BlockBreakInfo(3, BlockToolType::NONE, 0, 9));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
//        for($i = 0; $i <= 100; $i++){
//            $block = $this->getLevel()->getBlockAt(mt_rand(0, 15), mt_rand(0, 7), mt_rand(0, 15));
//
//            if($block->getId() === BlockLegacyIds::AIR){
//                $this->getLevel()->setBlock($block, $this, true);
//                $this->getLevel()->setBlock($this, BlockFactory::get(BlockLegacyIds::AIR));
//
//                for($i = 0; $i <= 100; $i++){
//                    $this->getLevel()->addParticle(new PortalParticle($this));
//                }
//                return true;
//            }
//        }
        return true;
    }
}
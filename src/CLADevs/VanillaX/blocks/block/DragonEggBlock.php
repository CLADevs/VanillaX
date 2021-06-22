<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\Player;

class DragonEggBlock extends Transparent{

    public function __construct(){
        parent::__construct(self::DRAGON_EGG, 0, "Dragon Egg");
    }

    public function getBlastResistance(): float{
        return 9;
    }

    public function getHardness(): float{
        return 3;
    }

    public function onActivate(Item $item, Player $player = null): bool{
//        for($i = 0; $i <= 100; $i++){
//            $block = $this->getLevel()->getBlockAt(mt_rand(0, 15), mt_rand(0, 7), mt_rand(0, 15));
//
//            if($block->getId() === BlockIds::AIR){
//                $this->getLevel()->setBlock($block, $this, true);
//                $this->getLevel()->setBlock($this, BlockFactory::get(BlockIds::AIR));
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
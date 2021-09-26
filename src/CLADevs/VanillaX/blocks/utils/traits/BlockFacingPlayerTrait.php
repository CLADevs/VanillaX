<?php

namespace CLADevs\VanillaX\blocks\utils\traits;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

trait BlockFacingPlayerTrait{

    private int $facing = 0;

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($player !== null){
            $facing = [
                4 => 3,
                3 => 2,
                2 => 0,
                5 => 1
            ];
            $this->facing = $facing[$player->getHorizontalFacing()];
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    protected function writeStateToMeta(): int{
        return $this->facing;
    }
}

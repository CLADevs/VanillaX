<?php

namespace CLADevs\VanillaX\blocks\utils\facing;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

trait FacingPlayerTrait{
    use \pocketmine\block\utils\AnyFacingTrait;

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($player !== null){
            $this->facing = match($player->getHorizontalFacing()){
                Facing::WEST => Facing::SOUTH,
                Facing::SOUTH => Facing::NORTH,
                Facing::EAST => Facing::UP,
                default => $player->getHorizontalFacing()
            };
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    protected function writeStateToMeta(): int{
        return $this->facing;
    }
}

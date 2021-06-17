<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\DropperTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DropperBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::DROPPER, $meta);
    }

    public function getHardness(): float{
        return 3.5;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $faces = [
            Vector3::SIDE_DOWN => 0,
            Vector3::SIDE_UP => 1,
            Vector3::SIDE_NORTH => 2,
            Vector3::SIDE_SOUTH => 3,
            Vector3::SIDE_WEST => 4,
            Vector3::SIDE_EAST => 5
        ];
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::DROPPER, DropperTile::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $tile = $this->getLevel()->getTile($this);

            if($tile instanceof DropperTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }
}
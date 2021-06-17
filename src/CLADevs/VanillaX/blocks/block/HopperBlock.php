<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\HopperTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class HopperBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::HOPPER_BLOCK, $meta, "Hopper");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::HOPPER, HopperTile::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $tile = $this->getLevel()->getTile($this);

            if($tile instanceof HopperTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }
}
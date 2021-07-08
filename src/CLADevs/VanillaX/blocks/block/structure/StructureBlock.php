<?php

namespace CLADevs\VanillaX\blocks\block\structure;

use CLADevs\VanillaX\blocks\tile\StructureBlockTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\Block;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class StructureBlock extends Solid implements NonCreativeItemTrait{

    public function __construct(int $meta = 0){
        parent::__construct(self::STRUCTURE_BLOCK, $meta, "Structure Block");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::STRUCTURE_BLOCK, StructureBlockTile::class);
        return true;
    }
}
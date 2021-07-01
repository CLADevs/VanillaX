<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\CauldronTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CauldronBlock extends Transparent{

    const EMPTY_CAULDRON = 0;
    const LEVEL_START = 1;
    const LEVEL_START_2 = 2;
    const LEVEL_MIDDLE = 3;
    const LEVEL_MIDDLE_2 = 4;
    const LEVEL_NEARLY_FULL = 5;
    const LEVEL_FULL = 6;

    public function __construct(int $meta = 0){
        parent::__construct(self::CAULDRON_BLOCK, $meta, "Cauldron", ItemIds::CAULDRON);
    }

    public function getHardness(): float{
        return 2;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::CAULDRON, CauldronTile::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        return true;
    }
}
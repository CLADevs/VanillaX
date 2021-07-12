<?php

namespace CLADevs\VanillaX\blocks\block\structure;

use CLADevs\VanillaX\blocks\tile\StructureBlockTile;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class StructureBlock extends Opaque implements NonCreativeItemTrait{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::STRUCTURE_BLOCK, 0, null, StructureBlockTile::class), "Structure Block", new BlockBreakInfo(-1, BlockToolType::NONE, 0, 3600000));
    }
}
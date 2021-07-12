<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;

class StructureBlockTile extends Spawnable{

    const TAG_ANIMATION_MODE = "animationMode";
    const TAG_ANIMATION_SECONDS = "animationSeconds";
    const TAG_DATA = "data";
    const TAG_IGNORE_ENTITIES = "ignoreEntities";
    const TAG_INCLUDE_PLAYERS = "includePlayers";
    const TAG_INTEGRITY = "integrity";
    const TAG_MIRROR = "mirror";
    const TAG_REDSTONE_SAVE_MODE = "redstoneSaveMode";
    const TAG_REMOVE_BLOCKS = "removeBlocks";
    const TAG_ROTATION = "rotation";
    const TAG_SHOW_BOUNDING_BOX = "showBoundingBox";
    const TAG_STRUCTURE_NAME = "structureName";
    const TAG_STRUCTURE_X_OFFSET = "xStructureOffset";
    const TAG_STRUCTURE_X_SIZE = "xStructureSize";
    const TAG_STRUCTURE_Y_OFFSET = "yStructureOffset";
    const TAG_STRUCTURE_Y_SIZE = "yStructureSize";
    const TAG_STRUCTURE_Z_OFFSET = "zStructureOffset";
    const TAG_STRUCTURE_Z_SIZE = "zStructureSize";

    const TILE_ID = TileVanilla::STRUCTURE_BLOCK;
    const TILE_BLOCK = BlockLegacyIds::STRUCTURE_BLOCK;

    public function readSaveData(CompoundTag $nbt): void{
    }

    protected function writeSaveData(CompoundTag $nbt): void{
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
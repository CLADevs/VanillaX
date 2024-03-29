<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\blocks\TileIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\FlowerPot;
use pocketmine\nbt\tag\CompoundTag;

class FlowerPotTile extends FlowerPot{

    private const TAG_ITEM = "item";
    private const TAG_ITEM_DATA = "mData";
    private const TAG_NAME = "name";
    private const TAG_PLANT_BLOCK = "PlantBlock";

    const TILE_ID = TileIds::FLOWER_POT;
    const TILE_BLOCK = BlockLegacyIds::FLOWER_POT_BLOCK;

    protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
        $plant = $this->getPlant();

        if($plant !== null){
            if($plant->getId() === BlockIds::CRIMSON_ROOTS || $plant->getId() ===BlockIds::WARPED_ROOTS){
                $tag = new CompoundTag();
                $tag->setString(self::TAG_NAME, "minecraft:" . strtolower(str_replace(" ", "_", $plant->getName())));
                $nbt->setTag(self::TAG_PLANT_BLOCK, $tag);
            }else{
                $nbt->setShort(self::TAG_ITEM, $plant->getId());
                $nbt->setInt(self::TAG_ITEM_DATA, $plant->getMeta());
            }
        }
    }
}
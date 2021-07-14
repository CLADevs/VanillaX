<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;

class CauldronTile extends Spawnable{

    const TAG_POTION_ID = "PotionId";
    const TAG_POTION_TYPE = "PotionType";
    const TAG_CUSTOM_COLOR = "CustomColor";

    const TILE_ID = TileVanilla::CAULDRON;
    const TILE_BLOCK = BlockLegacyIds::CAULDRON_BLOCK;

    private int $potionId = -1;
    private int $potionType = -1;
    private ?int $customColor = null;

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_POTION_ID)) !== null){
            $this->potionId = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_POTION_TYPE)) !== null){
            $this->potionType = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_CUSTOM_COLOR)) !== null){
            $this->customColor = $tag->getValue();
        }
        $this->setDirty();
        BlockManager::onChange($this);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setShort(self::TAG_POTION_ID, $this->potionId);
        $nbt->setByte(self::TAG_POTION_TYPE, $this->potionType);
        if($this->customColor !== null){
            $nbt->setInt(self::TAG_CUSTOM_COLOR, $this->customColor);
        }elseif($nbt->getTag(self::TAG_CUSTOM_COLOR) !== null){
            $nbt->removeTag(self::TAG_CUSTOM_COLOR);
        }
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->writeSaveData($nbt);
    }
}
<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\tile\Spawnable;

class CauldronTile extends Spawnable{

    const TAG_POTION_ID = "PotionId";
    const TAG_POTION_TYPE = "PotionType";
    const TAG_CUSTOM_COLOR = "CustomColor";

    const TILE_ID = TileVanilla::CAULDRON;
    const TILE_BLOCK = BlockLegacyIds::CAULDRON_BLOCK;

    private int $potionId = -1;
    private int $potionType = -1;
    private ?int $customColor = null;

    protected function readSaveData(CompoundTag $nbt): void{
        if($nbt->hasTag(self::TAG_POTION_ID, ShortTag::class)){
            $this->potionId = $nbt->getShort(self::TAG_POTION_ID);
        }
        if($nbt->hasTag(self::TAG_POTION_TYPE, ByteTag::class)){
            $this->potionType = $nbt->getByte(self::TAG_POTION_TYPE);
        }
        if($nbt->hasTag(self::TAG_CUSTOM_COLOR, IntTag::class)){
            $this->customColor = $nbt->getInt(self::TAG_POTION_ID);
        }
        $this->onChanged();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setShort(self::TAG_POTION_ID, $this->potionId);
        $nbt->setByte(self::TAG_POTION_TYPE, $this->potionType);
        if($this->customColor !== null){
            $nbt->setInt(self::TAG_CUSTOM_COLOR, $this->customColor);
        }elseif($nbt->hasTag(self::TAG_CUSTOM_COLOR)){
            $nbt->removeTag(self::TAG_CUSTOM_COLOR);
        }
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->writeSaveData($nbt);
    }
}
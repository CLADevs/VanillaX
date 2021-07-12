<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Furnace;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;

class FurnaceTile extends Furnace{

    const TILE_ID = TileVanilla::FURNACE;
    const TILE_BLOCK = BlockLegacyIds::FURNACE;

    private float $xpHolder = 0.0;

    protected function writeSaveData(CompoundTag $nbt): void{
        parent::writeSaveData($nbt);
        $nbt->setFloat("xpHolder", $this->xpHolder);
    }

    public function readSaveData(CompoundTag $nbt): void{
        parent::readSaveData($nbt);

        if(($tag = $nbt->getTag("xpHolder")) !== null){
            $this->xpHolder = $tag->getValue();
        }
        //todo give xp when received output item
//        $this->inventory->setEventProcessor(new class($this) implements InventoryEventProcessor{
//            private FurnaceTile $furnace;
//
//            public function __construct(FurnaceTile $furnace){
//                $this->furnace = $furnace;
//            }
//
//            public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem, Item $newItem) : ?Item{
//                if($slot === 2 && $oldItem->getId() !== ItemIds::AIR && $newItem->getId() === ItemIds::AIR){
//                    $this->furnace->dropXpHolder($this->furnace->asPosition());
//                }
//                $this->furnace->scheduleUpdate();
//                return $newItem;
//            }
//        });
    }

    public function getXpHolder(): float{
        return $this->xpHolder;
    }

    public function setXpHolder(float $xpHolder): void{
        $this->xpHolder = $xpHolder;
    }

    public function dropXpHolder(Position $position): void{
        $xpHolder = $this->xpHolder;

        if($xpHolder > 0.1){
            $position->getWorld()->dropExperience($position, intval($xpHolder * 10));
            $this->xpHolder = 0.0;
        }
    }
}
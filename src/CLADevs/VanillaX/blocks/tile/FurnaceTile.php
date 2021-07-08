<?php

namespace CLADevs\VanillaX\blocks\tile;

use pocketmine\block\BlockIds;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryEventProcessor;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\tile\Furnace;

class FurnaceTile extends Furnace{

    const TILE_ID = self::FURNACE;
    const TILE_BLOCK = BlockIds::FURNACE;

    private float $xpHolder = 0.0;

    protected function writeSaveData(CompoundTag $nbt): void{
        parent::writeSaveData($nbt);
        $nbt->setFloat("xpHolder", $this->xpHolder);
    }

    protected function readSaveData(CompoundTag $nbt): void{
        parent::readSaveData($nbt);

        if($nbt->hasTag("xpHolder", FloatTag::class)){
            $this->xpHolder = $nbt->getFloat("xpHolder", 0.0);
        }
        $this->inventory->setEventProcessor(new class($this) implements InventoryEventProcessor{
            private FurnaceTile $furnace;

            public function __construct(FurnaceTile $furnace){
                $this->furnace = $furnace;
            }

            public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem, Item $newItem) : ?Item{
                if($slot === 2 && $oldItem->getId() !== ItemIds::AIR && $newItem->getId() === ItemIds::AIR){
                    $this->furnace->dropXpHolder($this->furnace->asPosition());
                }
                $this->furnace->scheduleUpdate();
                return $newItem;
            }
        });
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
            $position->getLevel()->dropExperience($position, intval($xpHolder * 10));
            $this->xpHolder = 0.0;
        }
    }
}
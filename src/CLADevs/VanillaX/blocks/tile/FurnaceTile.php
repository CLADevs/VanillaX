<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\TileIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Furnace;
use pocketmine\crafting\FurnaceType;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;
use pocketmine\world\World;

class FurnaceTile extends Furnace{

    const TAG_XP_HOLDER = "xpHolder";

    const TILE_ID = TileIds::FURNACE;
    const TILE_BLOCK = BlockLegacyIds::FURNACE;

    private float $xpHolder = 0.0;

    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);
        $this->inventory->getListeners()->add(new CallbackInventoryListener(
            function(Inventory $unused, int $slot, Item $oldItem): void{
                $newItem = $this->inventory->getItem($slot);

                if($slot === 2 && $oldItem->getId() !== ItemIds::AIR && $newItem->getId() === ItemIds::AIR){
                    $this->dropXpHolder($this->getPosition());
                }
            },
            null
        ));
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        parent::writeSaveData($nbt);
        $nbt->setFloat(self::TAG_XP_HOLDER, $this->xpHolder);
    }

    public function readSaveData(CompoundTag $nbt): void{
        parent::readSaveData($nbt);

        if(($tag = $nbt->getTag(self::TAG_XP_HOLDER)) !== null){
            $this->xpHolder = $tag->getValue();
        }
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

    public function getFurnaceType(): FurnaceType{
        return FurnaceType::FURNACE();
    }
}
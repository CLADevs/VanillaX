<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\TileIds;
use CLADevs\VanillaX\inventories\types\BrewingStandInventory;
use CLADevs\VanillaX\inventories\types\HopperInventory;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Furnace;
use pocketmine\block\tile\Spawnable;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class HopperTile extends Spawnable implements Container{
use ContainerTrait;

    const TILE_ID = TileIds::HOPPER;
    const TILE_BLOCK = BlockLegacyIds::HOPPER_BLOCK;

    private int $facing = -1;
    private int $transferCooldown = 0;

    private HopperInventory $inventory;

    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);
        $this->inventory = new HopperInventory($this->getPosition());
    }

    public function getInventory(): HopperInventory{
        return $this->inventory;
    }

    public function getRealInventory(): HopperInventory{
        return $this->inventory;
    }

    public function getFacingBlock(): Block{
        return $this->getPosition()->getWorld()->getBlock($this->getPosition())->getSide($this->facing);
    }

    public function transferItems(): void{
        $block = $this->getFacingBlock();

        if(in_array($block->getId(), [BlockLegacyIds::HOPPER_BLOCK, BlockLegacyIds::FURNACE, BlockLegacyIds::BREWING_STAND_BLOCK, BlockLegacyIds::CHEST])){
            /** @var HopperTile|Furnace|BrewingStandTile|Chest $tile */
            $tile = $this->getPosition()->getWorld()->getTile($block->getPosition());
            $inventory = $tile->getInventory();

            if($tile instanceof HopperTile && $tile->getFacingBlock()->getPosition()->equals($this->getPosition())){
                //Stops infinite loop
                return;
            }

            foreach($this->getInventory()->getContents() as $slot => $item){
                if($inventory->canAddItem($item)){
                    if($inventory instanceof BrewingStandInventory){
                        $ingredientItem = $inventory->getItem(BrewingStandInventory::INGREDIENT_SLOT);

                        if(!$ingredientItem->isNull()){
                            if($item->getId() !== ItemIds::BLAZE_POWDER || $ingredientItem->getCount() >= $ingredientItem->getMaxStackSize()){
                                break;
                            }
                        }
                    }
                    $maxCount = $item->getCount();
                    $inventory->addItem($item->setCount(1));
                    $this->getInventory()->setItem($slot, $item->setCount($maxCount - 1));
                    break;
                }
            }
        }
    }

    public function getTransferCooldown(): int{
        return $this->transferCooldown;
    }

    public function setTransferCooldown(int $transferCooldown): void{
        $this->transferCooldown = $transferCooldown;
    }

    public function decreaseTransferCooldown(): void{
        $this->transferCooldown--;
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->facing = $this->getPosition()->getWorld()->getBlock($this->position)->getMeta();
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}
<?php

namespace CLADevs\VanillaX\entities\traits;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\tile\Container;

trait EntityContainer{

    abstract public function getEntity(): Entity;
    
    public function getContainerSaveName(): string{
        return Container::TAG_ITEMS;
    }

    protected function loadItems(CompoundTag $tag): void{
        if($tag->hasTag($this->getContainerSaveName(), ListTag::class)){
            $inventoryTag = $tag->getListTag($this->getContainerSaveName());

            /** @var CompoundTag $itemNBT */
            foreach($inventoryTag as $itemNBT){
                $this->setItem(Item::nbtDeserialize($itemNBT), $itemNBT->getByte("Slot"));
            }
        }
    }

    protected function saveItems(CompoundTag $tag): void{
        $items = [];
        foreach($this->getContents() as $slot => $item){
            $items[] = $item->nbtSerialize($slot);
        }
        $tag->setTag(new ListTag($this->getContainerSaveName(), $items, NBT::TAG_Compound));
    }

    abstract public function setItem(Item $item, int $slot = 0): void;

    /**
     * @return Item[]
     */
    abstract public function getContents(): array;
}
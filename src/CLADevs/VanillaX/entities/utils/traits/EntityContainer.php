<?php

namespace CLADevs\VanillaX\entities\utils\traits;

use pocketmine\block\tile\Container;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

trait EntityContainer{

    public function getContainerSaveName(): string{
        return Container::TAG_ITEMS;
    }

    protected function loadItems(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag($this->getContainerSaveName())) !== null){
            $inventoryTag = $tag->getValue();

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
        $tag->setTag($this->getContainerSaveName(), new ListTag($items, NBT::TAG_Compound));
    }

    abstract public function setItem(Item $item, int $slot = 0): void;

    /**
     * @param bool $includeEmpty
     * @return Item[]
     */
    abstract public function getContents(bool $includeEmpty = false): array;
}
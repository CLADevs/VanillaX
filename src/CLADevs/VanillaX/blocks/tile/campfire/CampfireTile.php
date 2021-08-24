<?php

namespace CLADevs\VanillaX\blocks\tile\campfire;

use CLADevs\VanillaX\entities\utils\traits\EntityContainer;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use Exception;
use pocketmine\block\tile\Spawnable;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;

abstract class CampfireTile extends Spawnable implements NonAutomaticCallItemTrait{
    use EntityContainer;

    const TAG_ITEM_TIME = "ItemTimes";

    const MAX_COOK_TIME = 30;

    /** @var int[] */
    private array $recipes = [
        ItemIds::RAW_BEEF => ItemIds::COOKED_BEEF,
        ItemIds::RAW_CHICKEN => ItemIds::COOKED_CHICKEN,
        ItemIds::RAW_PORKCHOP => ItemIds::COOKED_PORKCHOP,
        ItemIds::RAW_MUTTON => ItemIds::COOKED_MUTTON,
        ItemIds::RAW_FISH => ItemIds::COOKED_FISH,
        ItemIds::RAW_SALMON => ItemIds::COOKED_SALMON,
        ItemIds::POTATO => ItemIds::BAKED_POTATO,
        ItemIds::KELP => ItemIds::DRIED_KELP
    ];

    /** @var Item[] */
    private array $items = [];
    /** @var int[] */
    private array $itemTime = [];

    public function close(): void{
        foreach($this->items as $item){
            $this->position->getWorld()->dropItem($this->position->add(0, 1, 0), $item);
        }
        $this->items = [];
        parent::close();
    }

    /**
     * @param Item $item
     * @param int|null $slot, null means to find free slot
     * @throws Exception
     */
    public function setItem(Item $item, ?int $slot = null): void{
        if($slot === null){
            $slot = count($this->items) + 1;
        }
        if($slot === null || $slot > 4 || $slot < 1){
            throw new Exception("Out of range campfire slot, received $slot expected 1, 2, 3 or 4");
        }
        if($item->isNull()){
            if(isset($this->items[$slot])) unset($this->items[$slot]);
        }else{
            $this->items[$slot] = $item;
        }
    }

    /**
     * @param Item $item
     * @return bool
     * @throws Exception
     */
    public function addItem(Item $item): bool{
        if(!$this->canAddItem($item)){
            return false;
        }
        $this->setItem($item);
        return true;
    }

    public function canCook(Item $item): bool{
        return isset($this->recipes[$item->getId()]);
    }

    public function canAddItem(Item $item): bool{
        if(count($this->items) >= 4){
            return false;
        }
        return $this->canCook($item);
    }

    public function setSlotTime(int $slot, int $time): void{
        $this->itemTime[$slot] = $time;
    }

    public function increaseSlotTime(int $slot): void{
        $this->setSlotTime($slot, $this->getItemTime($slot) + 1);
    }

    public function getItemTime(int $slot): int{
        return $this->itemTime[$slot] ?? 0;
    }

    /**
     * @return int[]
     */
    public function getRecipes(): array{
        return $this->recipes;
    }

    /**
     * @param bool $includeEmpty
     * @return Item[]
     */
    public function getContents(bool $includeEmpty = false): array{
        return $this->items;
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->loadItems($nbt);

        if(($tag = $nbt->getTag(self::TAG_ITEM_TIME)) !== null){
            /** @var IntTag $time */
            foreach($tag->getValue() as $slot => $time){
                $this->itemTime[$slot + 1] = $time->getValue();
            }
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);

        $times = [];
        foreach($this->itemTime as $time){
            $times[] = new IntTag($time);
        }
        $nbt->setTag(self::TAG_ITEM_TIME, new ListTag($times));
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        foreach($this->items as $slot => $item){
            $nbt->setTag("Item" . $slot, $item->nbtSerialize());
            $nbt->setInt("ItemTime" . $slot, $this->getItemTime($slot));
        }
    }
}
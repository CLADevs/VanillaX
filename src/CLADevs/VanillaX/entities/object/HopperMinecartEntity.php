<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\traits\EntityContainer;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\network\gamerules\GameRule;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class HopperMinecartEntity extends MinecartEntity implements EntityInteractable{
use EntityContainer;

    const NETWORK_ID = self::HOPPER_MINECART;

    private FakeBlockInventory $inventory;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->inventory = new FakeBlockInventory($this->subtract(0, 1), 5, BlockIds::HOPPER_BLOCK, WindowTypes::HOPPER);
        $this->loadItems($nbt);
    }

    public function saveNBT(): void{
        $this->saveItems($this->namedtag);
        parent::saveNBT();
    }

    public function kill(): void{
        if(GameRule::getGameRuleValue(GameRule::DO_ENTITY_DROPS, $this->getLevel())){
            foreach(array_merge($this->getContents(), [ItemFactory::get(ItemIds::MINECART_WITH_HOPPER)]) as $item){
                $this->getLevel()->dropItem($this, $item);
            }
        }
        parent::kill();
    }

    public function onInteract(EntityInteractResult $result): void{
        $result->getPlayer()->addWindow($this->inventory);
    }

    public function getContainerSaveName(): string{
        return "MinecartItems";
    }

    public function setItem(Item $item, int $slot = 0): void{
        $this->inventory->setItem($slot, $item);
    }

    public function getContents(bool $includeEmpty = false): array{
        return $this->inventory->getContents($includeEmpty);
    }
}
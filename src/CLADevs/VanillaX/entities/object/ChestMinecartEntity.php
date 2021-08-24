<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\traits\EntityContainer;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class ChestMinecartEntity extends MinecartEntity{
use EntityContainer;

    const NETWORK_ID = EntityIds::CHEST_MINECART;

    private FakeBlockInventory $inventory;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $pos = $this->getPosition();
        $pos->y += 1;
        $this->inventory = new FakeBlockInventory($pos);
        $this->loadItems($nbt);
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $this->saveItems($nbt);
        return $nbt;
    }

    public function kill(): void{
        if(GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            foreach(array_merge($this->getContents(), [ItemFactory::getInstance()->get(ItemIds::MINECART_WITH_CHEST)]) as $item){
                $this->getWorld()->dropItem($this->getPosition(), $item);
            }
        }
        parent::kill();
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $player->setCurrentWindow($this->inventory);
        return true;
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
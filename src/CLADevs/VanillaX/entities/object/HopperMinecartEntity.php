<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\utils\ContainerTrait;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use Exception;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;

class HopperMinecartEntity extends MinecartEntity{
use ContainerTrait;

    const NETWORK_ID = EntityIds::HOPPER_MINECART;

    private FakeBlockInventory $inventory;

    /**
     * @throws Exception
     */
    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $pos = $this->getPosition();
        $pos->y -= 1;
        $this->inventory = new FakeBlockInventory($pos, 5, BlockLegacyIds::HOPPER_BLOCK, WindowTypes::HOPPER);
        $this->loadItems($nbt);
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $this->saveItems($nbt);
        return $nbt;
    }

    public function kill(): void{
        if(GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            foreach(array_merge($this->getContents(), [ItemFactory::getInstance()->get(ItemIds::MINECART_WITH_HOPPER)]) as $item){
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

    public static function canRegister(): bool{
        return true;
    }
}
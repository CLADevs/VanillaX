<?php

namespace CLADevs\VanillaX\inventories\types;

use pocketmine\entity\Entity;
use pocketmine\inventory\BaseInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Player;

class OffhandInventory extends BaseInventory{

    const COLOR_BLACK = -1;
    const COLOR_NORMAL = 0;
    const COLOR_BROWN = 1;
    const COLOR_PURPLE = 2;
    const COLOR_GREEN = 3;
    const COLOR_YELLOW = 4;
    const COLOR_DARK_GREEN = 5;
    const COLOR_DARKISH = 6;

    const TAG_OFF_HAND_ITEM = "OffHandItem";

    private Player $player;

    public function __construct(Player $player){
        $this->player = $player;
        parent::__construct();
        $addWindow = true;

        if(($offhand = $player->getWindow(ContainerIds::OFFHAND)) !== null){

            if(!$offhand instanceof OffhandInventory){
                $player->removeWindow($offhand);
            }else{
                $addWindow = false;
            }
        }
        if($addWindow) $player->addWindow($this, ContainerIds::OFFHAND, true);
        $player->getDataPropertyManager()->setByte(Entity::DATA_COLOR, self::COLOR_NORMAL);
        if($player->namedtag->hasTag(self::TAG_OFF_HAND_ITEM)){
            $this->setItem(0, Item::nbtDeserialize($player->namedtag->getCompoundTag(self::TAG_OFF_HAND_ITEM)), true);
        }
        $this->sendContents($player);
    }

    public function getName(): string{
        return "Offhand";
    }

    public function getDefaultSize(): int{
        return 1;
    }

    public function onSlotChange(int $index, Item $before, bool $send): void{
        if($this->player->spawned) $this->sendContents();
    }

    /**
     * @param Player|Player[]|null $target
     */
    public function sendContents($target = null): void{
        if($target === null){
            $target = array_merge($this->player->getViewers(), [$this->player]);
        }
        if($target instanceof Player){
            $target = [$target];
        }
        $pk = new InventoryContentPacket();
        $pk->windowId = ContainerIds::OFFHAND;
        $pk->items = array_map([ItemStackWrapper::class, 'legacy'], $this->getContents(true));

        foreach($target as $player){
            if($player === $this->player){
                $player->dataPacket($pk);
            }else{
                $this->sendEquipment($player);
            }
        }
    }

    public function sendEquipment(Player $player): void{
        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->player->getId();
        $pk->item = ItemStackWrapper::legacy($this->getItem(0));
        $pk->inventorySlot = 1;
        $pk->hotbarSlot = 0;
        $pk->windowId = ContainerIds::OFFHAND;
        $player->dataPacket($pk);
    }

    public function equipItem(int $slot): void{
        $this->saveContents();
    }

    public function saveContents(): void{
        $this->player->namedtag->setTag($this->getItem(0)->nbtSerialize(-1, OffhandInventory::TAG_OFF_HAND_ITEM));
    }
}
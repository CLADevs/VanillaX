<?php

namespace CLADevs\VanillaX\inventories\types;

use pocketmine\inventory\BaseInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Player;

class OffhandInventory extends BaseInventory{

    const TAG_OFF_HAND_ITEM = "OffHandItem";

    private Player $player;

    public function __construct(Player $player){
        parent::__construct();
        $this->player = $player;
        if($player->namedtag->hasTag(self::TAG_OFF_HAND_ITEM)){
            $this->setItem(0, Item::nbtDeserialize($player->namedtag->getCompoundTag(self::TAG_OFF_HAND_ITEM)), true);
        }
    }

    public function getName(): string{
        return "Offhand";
    }

    public function getDefaultSize(): int{
        return 1;
    }

    /**
     * @param Player|Player[] $target
     */
    public function sendContents($target): void{
        if($target instanceof Player){
            $target = [$target];
        }

        $pk = new InventoryContentPacket();
        $pk->items = array_map([ItemStackWrapper::class, 'legacy'], $this->getContents(true));

        foreach($target as $player){
            $pk->windowId = ContainerIds::OFFHAND;
            $player->dataPacket($pk);
            $this->sendEquipment($player);
        }
    }

    public function onSlotChange(int $index, Item $before, bool $send): void{
        parent::onSlotChange($index, $before, $send);
        $this->sendEquipment($this->player->getViewers());
    }

    /**
     * @param int $index
     * @param Player|Player[] $target
     */
    public function sendSlot(int $index, $target): void{
        if($target instanceof Player){
            $target = [$target];
        }

        $pk = new InventorySlotPacket();
        $pk->inventorySlot = $index;
        $pk->item = ItemStackWrapper::legacy($this->getItem(0));

        foreach($target as $player){
            $pk->windowId = ContainerIds::OFFHAND;
            $player->dataPacket($pk);
            $this->sendEquipment($player);
        }
    }

    /**
     * @param Player[]|Player $target
     * @param bool $force
     */
    public function sendEquipment($target, bool $force = false): void{
        if($target instanceof Player){
            $target = [$target];
        }
        foreach($target as $player){
            if(!$force && strtolower($player->getName()) === strtolower($this->player->getName())){
                continue;
            }
            $pk = new MobEquipmentPacket();
            $pk->entityRuntimeId = $this->player->getId();
            $pk->item = ItemStackWrapper::legacy($this->getItem(0));
            $pk->inventorySlot = 1;
            $pk->hotbarSlot = 0;
            $pk->windowId = ContainerIds::OFFHAND;
            $player->dataPacket($pk);
        }
    }
}
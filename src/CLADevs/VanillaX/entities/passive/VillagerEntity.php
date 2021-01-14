<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use CLADevs\VanillaX\entities\traits\EntityInteractable;
use CLADevs\VanillaX\inventories\TradeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\Player;

class VillagerEntity extends LivingEntity implements EntityInteractable{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::VILLAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
    }

    public function getName(): string{
        return "Villager";
    }

    public function onInteract(Player $player, Item $item): void{
        $windowId = $player->addWindow(new TradeInventory($this));
        $stream = new NetworkLittleEndianNBTStream();
        $pk = new UpdateTradePacket();
        $pk->windowId = $windowId;
        $pk->windowType = WindowTypes::TRADING;
        $pk->windowSlotCount = 1;
        $pk->tradeTier = 0;
        $pk->traderEid = $this->getId();
        $pk->playerEid = $player->getId();
        $pk->displayName = "Cleric";
        $pk->isV2Trading = true;
        $pk->isWilling = false;
        //$pk->offers = ""; //TODO network NBT serialised compound of offers that villager has. (Gophertunnel)
        $pk->offers = $stream->write(ItemFactory::get(ItemIds::TNT)->nbtSerialize());
        $player->dataPacket($pk);
        var_dump("O");
    }
}
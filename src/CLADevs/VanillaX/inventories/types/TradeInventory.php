<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockIds;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\Player;

class TradeInventory extends FakeBlockInventory{

    private VillagerEntity $villager;

    public function __construct(VillagerEntity $holder){
        parent::__construct($holder, 2, BlockIds::AIR, WindowTypes::TRADING);
        $this->villager = $holder;
    }

    public function getName(): string{
        return "Trade";
    }

    public function getDefaultSize(): int{
        return 2;
    }

    public function getNetworkType(): int{
        return WindowTypes::TRADING;
    }

    public function onClose(Player $who): void{
        foreach($this->getContents() as $item){
            $who->dropItem($item);
        }
        $this->clearAll();
        VanillaX::getInstance()->getSessionManager()->get($who)->setRidingEntity(null);
        $this->villager->setCustomer(null);

        $pk = new ContainerClosePacket();
        $pk->windowId = 255;
        $pk->server = false;
        $who->dataPacket($pk);
        unset($this->viewers[spl_object_hash($who)]);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $pk = new UpdateTradePacket();
        $pk->windowId = $who->getWindowId($this);
        $pk->windowType = WindowTypes::TRADING;
        $pk->windowSlotCount = 0;
        $pk->tradeTier = 0;
        $pk->traderEid = $this->villager->getId();
        $pk->playerEid = $who->getId();
        $pk->displayName = $this->villager->getProfession()->getName();
        $pk->isV2Trading = true;
        $pk->isWilling = false;
        $pk->offers = $this->villager->getOfferBuffer();
        $who->dataPacket($pk);
    }

}
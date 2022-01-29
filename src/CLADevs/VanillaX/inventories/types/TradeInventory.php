<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\player\Player;

class TradeInventory extends FakeBlockInventory{

    private VillagerEntity $villager;

    public function __construct(VillagerEntity $holder){
        parent::__construct($holder->getPosition(), 2, BlockLegacyIds::AIR, WindowTypes::TRADING);
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
        VanillaX::getInstance()->getSessionManager()->get($who)->setTradingEntity(null);
        $this->villager->setCustomer(null);

        $pk = new ContainerClosePacket();
        $pk->windowId = 255;
        $pk->server = false;
        $who->getNetworkSession()->sendDataPacket($pk);
        unset($this->viewers[spl_object_hash($who)]);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $pk = new UpdateTradePacket();
        $pk->windowId = $who->getNetworkSession()->getInvManager()->getWindowId($this);
        $pk->windowType = WindowTypes::TRADING;
        $pk->windowSlotCount = 0;
        $pk->tradeTier = 0;
        $pk->traderActorUniqueId = $this->villager->getId();
        $pk->playerActorUniqueId = $who->getId();
        $pk->displayName = $this->villager->getProfession()->getName();
        $pk->isV2Trading = true;
        $pk->isEconomyTrading = false;
        $pk->offers = new CacheableNbt($this->villager->getOffers());
        $who->getNetworkSession()->sendDataPacket($pk);
    }

    public function getVillager(): VillagerEntity{
        return $this->villager;
    }
}
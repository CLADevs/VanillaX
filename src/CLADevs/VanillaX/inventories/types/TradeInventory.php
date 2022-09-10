<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\TemporaryInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\player\Player;

class TradeInventory extends FakeBlockInventory implements TemporaryInventory, RecipeInventory{

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
        VanillaX::getInstance()->getSessionManager()->get($who)->setTradingEntity(null);
        $this->villager->setCustomer(null);

        unset($this->viewers[spl_object_hash($who)]);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $pk = new UpdateTradePacket();
        $pk->windowId = $who->getNetworkSession()->getInvManager()->getCurrentWindowId();
        $pk->windowType = WindowTypes::TRADING;
        $pk->windowSlotCount = 0;
        $pk->tradeTier = $this->villager->getTier();
        $pk->traderActorUniqueId = $this->villager->getId();
        $pk->playerActorUniqueId = $who->getId();
        $pk->displayName = $this->villager->getProfession()->getName();
        $pk->isV2Trading = true;
        $pk->isEconomyTrading = true;
        $pk->offers = $this->villager->getOffers()->getNbt();
        $who->getNetworkSession()->sendDataPacket($pk);
    }

    public function getResultItem(Player $player, int $netId): ?Item{
        return null;
    }

    public function getVillager(): VillagerEntity{
        return $this->villager;
    }
}
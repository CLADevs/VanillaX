<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\villager\VillagerOffer;
use CLADevs\VanillaX\entities\utils\villager\VillagerTradeNBTStream;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\player\Player;

class TradeInventory extends FakeBlockInventory{

    private VillagerEntity $villager;

    private ?Item $buyAItem = null;
    private ?Item $sellItem = null;

    public function __construct(VillagerEntity $holder){
        parent::__construct($holder, 2, BlockLegacyIds::AIR, WindowTypes::TRADING);
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
        $who->dataPacket($pk);
        unset($this->viewers[spl_object_hash($who)]);
        $this->buyAItem = null;
        $this->sellItem = null;
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

    public function onTrade(Player $player, Item $source, Item $target): void{
        if($this->buyAItem === null){
            $this->buyAItem = $source->isNull() ? $target : $source;
        }elseif($this->sellItem === null){
            $this->sellItem = $source->isNull() ? $target : $source;
        }
        if($this->buyAItem !== null && $this->sellItem !== null && !$this->buyAItem->isNull() && !$this->sellItem->isNull()){
            /** @var CompoundTag $nbt */
            $nbt = (new NetworkLittleEndianNBTStream())->read($this->villager->getOfferBuffer());
            $recipes = $nbt->getValue()[VillagerTradeNBTStream::TAG_RECIPES]->getValue();

            /** @var ListTag $recipe */
            foreach($recipes as $key => $recipe){
                $value = $recipe->getValue();
                /** @var NamedTag $buyA */
                $buyA = $value[VillagerOffer::TAG_BUY_A]->getValue();
                $buyA = ItemFactory::getInstance()->get($buyA["id"]->getValue(), $buyA["Damage"]->getValue(), $buyA["Count"]->getValue());
                $sell = $value[VillagerOffer::TAG_SELL]->getValue();
                $sell = ItemFactory::getInstance()->get($sell["id"]->getValue(), $sell["Damage"]->getValue(), $sell["Count"]->getValue());
                $experience = $value[VillagerOffer::TAG_TRADER_EXP]->getValue();

                if($this->buyAItem->equalsExact($buyA) && $this->sellItem->equalsExact($sell)){
                    $this->buyAItem = null;
                    $this->sellItem = null;
                    $value[VillagerOffer::TAG_USES] = new IntTag(VillagerOffer::TAG_USES, $value[VillagerOffer::TAG_USES]->getValue() + 1);

                    $recipes[$key] = new CompoundTag($recipe->getName(), $value);
                    $nbt->setTag(new ListTag(VillagerTradeNBTStream::TAG_RECIPES, $recipes));

                    if($experience > 0){
                        $this->villager->setExperience($this->villager->getExperience() + $experience);
                    }
                    $this->villager->setOfferBuffer((new NetworkLittleEndianNBTStream())->write($nbt));
                    break;
                }
            }
        }
    }
}
<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\configuration\features\BlockFeature;
use CLADevs\VanillaX\configuration\Setting;
use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\inventories\transaction\EnchantTransaction;
use CLADevs\VanillaX\inventories\transaction\RepairTransaction;
use CLADevs\VanillaX\inventories\transaction\TradeTransaction;
use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\session\SessionManager;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionException;
use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\InventoryManager;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;
use pocketmine\network\mcpe\protocol\AnvilDamagePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\FilterTextPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\player\Player;

class InGamePacketHandlerX extends InGamePacketHandler{

    private Session $session;
    private ?RepairTransaction $repairTransaction = null;
    private ?EnchantTransaction $enchantTransaction = null;
    private ?TradeTransaction $tradeTransaction = null;

    public function __construct(Player $player, NetworkSession $session, InventoryManager $inventoryManager){
        parent::__construct($player, $session, $inventoryManager);
        $this->session = SessionManager::getInstance()->get($player);
    }

    public function handleActorEvent(ActorEventPacket $packet): bool{
        if($packet->eventId === ActorEvent::PLAYER_ADD_XP_LEVELS || $packet->eventId === ActorEvent::COMPLETE_TRADE){
            return true;
        }
        return parent::handleActorEvent($packet);
    }

    public function handlePlayerAction(PlayerActionPacket $packet): bool{
        if($packet->action === PlayerAction::SET_ENCHANTMENT_SEED){
            return true;
        }
        return parent::handlePlayerAction($packet);
    }

    public function handleActorPickRequest(ActorPickRequestPacket $packet): bool{
        return true;
    }

    public function handleAnvilDamage(AnvilDamagePacket $packet): bool{
        return true;
    }

    public function handleCommandBlockUpdate(CommandBlockUpdatePacket $packet): bool{
        return true;
    }

    public function handleFilterText(FilterTextPacket $packet): bool{
        return true;
    }

    public function handleInventoryTransaction(InventoryTransactionPacket $packet): bool{
        $trData = $packet->trData;

        if($trData instanceof NormalTransactionData){
            $session = $this->session;
            $player = $session->getPlayer();
            $inventory = $player->getCurrentWindow();

            if($inventory instanceof AnvilInventory || $inventory instanceof EnchantInventory || $inventory instanceof TradeInventory){
                $convertor = TypeConverterX::getInstance();
                $isRepairPart = false;
                $isEnchantPart = false;
                $isTradePart = false;
                $actions = [];

                foreach($trData->getActions() as $networkInventoryAction){
                    $networkInventoryAction = clone $networkInventoryAction;

                    if(in_array($networkInventoryAction->sourceType, [NetworkInventoryAction::SOURCE_TODO, TypeConverterX::SOURCE_CRAFT_SLOT])){
                        switch($networkInventoryAction->windowId){
                            case NetworkInventoryAction::SOURCE_TYPE_ANVIL_RESULT:
                            case TypeConverterX::SOURCE_TYPE_ANVIL_INPUT:
                            case TypeConverterX::SOURCE_TYPE_ANVIL_MATERIAL:
                                $isRepairPart = true;
                                break;
                            case NetworkInventoryAction::SOURCE_TYPE_ENCHANT_OUTPUT:
                            case TypeConverterX::SOURCE_TYPE_ENCHANT_INPUT:
                            case TypeConverterX::SOURCE_TYPE_ENCHANT_MATERIAL:
                                $isEnchantPart = true;
                                break;
                            case TypeConverterX::SOURCE_TYPE_TRADE_OUTPUT:
                            case TypeConverterX::SOURCE_TYPE_TRADE_INPUT:
                                $isTradePart = true;
                                break;
                        }
                    }
                    $actions[] = $convertor->createInventoryAction($networkInventoryAction, $player, $player->getNetworkSession()->getInvManager());
                }
                if($isRepairPart && BlockFeature::getInstance()->isBlockEnabled(VanillaBlocks::ANVIL())){
                    return $this->handleRepairTransaction($player, $actions, $trData);
                }elseif($isEnchantPart && BlockFeature::getInstance()->isBlockEnabled(VanillaBlocks::ENCHANTING_TABLE())){
                    return $this->handleEnchantTransaction($player, $actions, $trData);
                }elseif($isTradePart && Setting::getInstance()->isTradeEnabled()){
                    return $this->handleTradeTransaction($player, $actions, $trData, $inventory->getVillager());
                }
            }
        }
        return parent::handleInventoryTransaction($packet);
    }

    /**
     * @param Player $player
     * @param InventoryAction[] $actions
     * @param NormalTransactionData $trData
     * @return bool
     */
    private function handleRepairTransaction(Player $player, array $actions, NormalTransactionData $trData): bool{
        try{
            if($this->repairTransaction === null){
                $this->repairTransaction = new RepairTransaction($player, $actions);
            }else{
                foreach($actions as $action){
                    $this->repairTransaction->addAction($action);
                }
            }
            if($this->repairTransaction->canExecute()){
                $this->repairTransaction->execute();
                $this->repairTransaction = null;
            }
            return true;
        }catch(TransactionException $e){
            foreach($this->repairTransaction->getInventories() as $inventory){
                $player->getNetworkSession()->getInvManager()->syncContents($inventory);
            }
            $this->repairTransaction = null;
            $logger = $player->getNetworkSession()->getLogger();
            $logger->debug("Failed to execute anvil inventory transaction: " . $e->getMessage());
            $logger->debug("Actions: " . json_encode($trData->getActions()));
            return false;
        }
    }

    /**
     * @param Player $player
     * @param InventoryAction[] $actions
     * @param NormalTransactionData $trData
     * @return bool
     */
    private function handleEnchantTransaction(Player $player, array $actions, NormalTransactionData $trData): bool{
        try{
            if($this->enchantTransaction === null){
                $this->enchantTransaction = new EnchantTransaction($player, $actions);
            }else{
                foreach($actions as $action){
                    $this->enchantTransaction->addAction($action);
                }
            }
            if($this->enchantTransaction->canExecute()){
                $this->enchantTransaction->execute();
                $this->enchantTransaction = null;
            }
            return true;
        }catch(TransactionException $e){
            foreach($this->enchantTransaction->getInventories() as $inventory){
                $player->getNetworkSession()->getInvManager()->syncContents($inventory);
            }
            $this->enchantTransaction = null;
            $logger = $player->getNetworkSession()->getLogger();
            $logger->debug("Failed to execute enchant inventory transaction: " . $e->getMessage());
            $logger->debug("Actions: " . json_encode($trData->getActions()));
            return false;
        }
    }

    /**
     * @param Player $player
     * @param InventoryAction[] $actions
     * @param NormalTransactionData $trData
     * @return bool
     */
    private function handleTradeTransaction(Player $player, array $actions, NormalTransactionData $trData, VillagerEntity $villager): bool{
        try{
            if($this->tradeTransaction === null){
                $this->tradeTransaction = new TradeTransaction($player, $actions, $villager);
            }else{
                foreach($actions as $action){
                    $this->tradeTransaction->addAction($action);
                }
            }
            if($this->tradeTransaction->canExecute()){
                $this->tradeTransaction->execute();
                $this->tradeTransaction = null;
            }
            return true;
        }catch(TransactionException $e){
            foreach($this->tradeTransaction->getInventories() as $inventory){
                $player->getNetworkSession()->getInvManager()->syncContents($inventory);
            }
            $this->tradeTransaction = null;
            $logger = $player->getNetworkSession()->getLogger();
            $logger->debug("Failed to execute trade inventory transaction: " . $e->getMessage());
            $logger->debug("Actions: " . json_encode($trData->getActions()));
            return false;
        }
    }
}
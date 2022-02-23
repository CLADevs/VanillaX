<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\configuration\features\BlockFeature;
use CLADevs\VanillaX\configuration\Setting;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\event\player\PlayerEntityPickEvent;
use CLADevs\VanillaX\inventories\transaction\EnchantTransaction;
use CLADevs\VanillaX\inventories\transaction\RepairTransaction;
use CLADevs\VanillaX\inventories\transaction\TradeTransaction;
use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\inventories\utils\TypeConverterX;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use CLADevs\VanillaX\VanillaX;
use Exception;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\java\GameModeIdMap;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\TransactionException;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\InventoryManager;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;
use pocketmine\network\mcpe\protocol\AnvilDamagePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\FilterTextPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class InGamePacketHandlerX extends InGamePacketHandler{

    private Session $session;
    private ?RepairTransaction $repairTransaction = null;
    private ?EnchantTransaction $enchantTransaction = null;
    private ?TradeTransaction $tradeTransaction = null;

    public function __construct(Player $player, NetworkSession $session, InventoryManager $inventoryManager){
        parent::__construct($player, $session, $inventoryManager);
        $this->session = SessionManager::getInstance()->get($player);
    }

    public function handleAnvilDamage(AnvilDamagePacket $packet): bool{
        return true;
    }

    public function handleFilterText(FilterTextPacket $packet): bool{
        return true;
    }

    public function handleContainerClose(ContainerClosePacket $packet): bool{
        $player = $this->session->getPlayer();

        if($packet->windowId === 255){
            $inv = $player->getCurrentWindow();

            if($inv instanceof TradeInventory){
                $player->removeCurrentWindow();
            }
        }
        parent::handleContainerClose($packet);
        return true;
    }

    public function handleSetPlayerGameType(SetPlayerGameTypePacket $packet): bool{
        $player = $this->session->getPlayer();

        if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->setGamemode(GameModeIdMap::getInstance()->fromId($packet->gamemode));
        }
        return true;
    }

    public function handleSetDefaultGameType(SetDefaultGameTypePacket $packet): bool{
        $player = $this->session->getPlayer();

        if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            Server::getInstance()->getConfigGroup()->setConfigInt("gamemode", $packet->gamemode);
        }
        return true;
    }

    public function handleSetDifficulty(SetDifficultyPacket $packet): bool{
        $player = $this->session->getPlayer();

        if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->getWorld()->setDifficulty($packet->difficulty);
        }
        return true;
    }

    public function handleActorEvent(ActorEventPacket $packet): bool{
        if($packet->eventId === ActorEvent::PLAYER_ADD_XP_LEVELS || $packet->eventId === ActorEvent::COMPLETE_TRADE){
            return true;
        }
        return parent::handleActorEvent($packet);
    }

    public function handleActorPickRequest(ActorPickRequestPacket $packet): bool{
        $player = $this->session->getPlayer();
        $entity = $player->getWorld()->getEntity($packet->actorUniqueId);

        if($entity instanceof VanillaEntity && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $result = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityManager::getInstance()->getEntity($entity->getNetworkTypeId())->getId());
            $ev = new PlayerEntityPickEvent($player, $entity, $result);
            $ev->call();

            if(!$ev->isCancelled()){
                $player->getInventory()->setItemInHand($ev->getResultItem());
            }
        }
        return true;
    }

    public function handlePlayerAction(PlayerActionPacket $packet): bool{
        switch($packet->action){
            case PlayerAction::SET_ENCHANTMENT_SEED:
                return true;
            case PlayerAction::START_GLIDE:
            case PlayerAction::STOP_GLIDE:
                $this->session->setGliding($packet->action === PlayerAction::START_GLIDE);
                break;
            case PlayerAction::START_SWIMMING:
            case PlayerAction::STOP_SWIMMING:
                $this->session->setSwimming($packet->action === PlayerAction::START_SWIMMING);
                break;
        }
        return parent::handlePlayerAction($packet);
    }

    /**
     * @throws Exception
     */
    public function handleCommandBlockUpdate(CommandBlockUpdatePacket $packet): bool{
        $player = $this->session->getPlayer();
        $position = new Position($packet->blockPosition->getX(), $packet->blockPosition->getY(), $packet->blockPosition->getZ(), $player->getWorld());
        $tile = $position->getWorld()->getTile($position);

        if($tile instanceof CommandBlockTile){
            $tile->handleCommandBlockUpdate($player, $packet);
            return true;
        }
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
        }else{
            $this->handleInteractableButton($packet);
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
        //try and see if enchant transaction can happen
        try{
            if($this->enchantTransaction === null){
                $this->enchantTransaction = new EnchantTransaction($player, $actions);
            }else{
                foreach($actions as $action){
                    $this->enchantTransaction->addAction($action);
                }
            }
            // check if enchant can execute
            if($this->enchantTransaction->canExecute()){
                $this->enchantTransaction->execute();
                $this->enchantTransaction = null;
            }
            return true;
        }catch(TransactionException $e){
            // catch any errors?
            foreach($this->enchantTransaction->getInventories() as $inventory){
                $player->getNetworkSession()->getInvManager()->syncContents($inventory);
            }
            $this->enchantTransaction = null;
            // debug
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

    private function handleInteractableButton(InventoryTransactionPacket $packet): void{
        $player = $this->session->getPlayer();

        if($packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT){
            $entity = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();
            $clickPos = $packet->trData->getClickPosition();
            $button = null;

            if(is_string($currentButton) && count($packet->trData->getActions()) < 1){
                if($entity instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for entity */
                    $entity->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton, $clickPos));
                }
                if($item instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for item */
                    $item->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton, $clickPos));
                }
            }

            if($entity instanceof EntityInteractable){
                /** If a player interacts with entity with a item */
                if($button === null || $button->canInteractQueue()){
                    $entity->onInteract(new EntityInteractResult($player, $item, null, $clickPos, $currentButton));
                }
            }
            if($item instanceof EntityInteractable){
                /** If a player interacts with entity with a item that has EntityInteractable traits */
                $item->onInteract(new EntityInteractResult($player, null, $entity));
            }
        }elseif($packet->trData instanceof UseItemTransactionData && $packet->trData->getActionType() === UseItemTransactionData::ACTION_CLICK_AIR){
            $entity = VanillaX::getInstance()->getSessionManager()->get($player)->getRidingEntity();
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();

            if(is_string($currentButton) && count($packet->trData->getActions()) < 1){
                if($entity instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for entity */
                    $entity->onButtonPressed(new InteractButtonResult($player, $item, $currentButton));
                }
                if($item instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for item */
                    $item->onButtonPressed(new InteractButtonResult($player, $item, $currentButton));
                }
            }
        }
    }
}

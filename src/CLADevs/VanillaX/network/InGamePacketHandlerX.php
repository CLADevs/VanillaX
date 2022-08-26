<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\event\player\PlayerEntityPickEvent;
use CLADevs\VanillaX\inventories\ItemStackRequestHandler;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\items\types\CrossbowItem;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use CLADevs\VanillaX\VanillaX;
use Exception;
use pocketmine\data\java\GameModeIdMap;
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
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class InGamePacketHandlerX extends InGamePacketHandler{

    private Session $session;
    private ItemStackRequestHandler $itemStackRequestHandler;

    public function __construct(Player $player, NetworkSession $session, InventoryManager $inventoryManager){
        parent::__construct($player, $session, $inventoryManager);
        $this->session = SessionManager::getInstance()->get($player);
        $this->itemStackRequestHandler = new ItemStackRequestHandler($session);
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
            $result = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityManager::getInstance()->getEntityInfo($entity::getNetworkTypeId())->getLegacyId());
            $ev = new PlayerEntityPickEvent($player, $entity, $result);
            $ev->call();

            if(!$ev->isCancelled()){
                $player->getInventory()->setItemInHand($ev->getResultItem());
            }
        }
        return true;
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
        $player = $this->session->getPlayer();
        $trData = $packet->trData;

        switch($trData->getTypeId()){
            case UseItemTransactionData::ID:
            case UseItemOnEntityTransactionData::ID:
                $this->handleInteractableButton($packet);

                if($trData instanceof UseItemTransactionData && $trData->getActionType() === UseItemTransactionData::ACTION_CLICK_AIR && $player->isUsingItem()){
                    $item = $player->getInventory()->getItemInHand();

                    if($item instanceof CrossbowItem){
                        $player->useHeldItem();
                        return true;
                    }
                }
                break;
        }
        return parent::handleInventoryTransaction($packet);
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

    public function handleItemStackRequest(ItemStackRequestPacket $packet): bool{
        return $this->itemStackRequestHandler->handleItemStackRequest($packet);
    }
}
<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\entities\object\ArmorStandEntity;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\object\FallingBlock;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\Player;
use pocketmine\Server;

class VanillaListener implements Listener{

    /**
     * @var array
     * this prevents armor stand glitch where you could dupe
     */
    public array $armorStandItemsQueue = [];

    public function handlePacketSend(DataPacketSendEvent $event): void{
        if(!$event->isCancelled()){
            $packet = $event->getPacket();

            if($packet instanceof AvailableCommandsPacket){
                foreach(VanillaX::getInstance()->getCommandManager()->getCommands() as $key => $command){
                    if(($arg = $command->getCommandArg()) !== null){
                        $command = $packet->commandData[strtolower($key)];
                        $command->flags = $arg->getFlags();
                        $command->permission = $arg->getPermission();
                        $command->overloads = $arg->getOverload();
                    }
                }
            }
        }
    }

    public function handlePacketReceive(DataPacketReceiveEvent $event): void{
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($packet instanceof CommandBlockUpdatePacket){
            $position = new Position($packet->x, $packet->y, $packet->z, $player->getLevel());
            $tile = $position->getLevel()->getTile($position);

            if($tile instanceof CommandBlockTile){
                $tile->handleCommandBlockUpdateReceive($packet);
            }
        }else{
            $window = $session->getCurrentWindow();

            if($window !== null){
                $window->handlePacket($player, $packet);
            }
        }
        if($packet instanceof PlayerActionPacket && in_array($packet->action, [PlayerActionPacket::ACTION_START_GLIDE, PlayerActionPacket::ACTION_STOP_GLIDE])){
            $session->setGliding($packet->action === PlayerActionPacket::ACTION_START_GLIDE);
        }elseif($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData){
            if($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT){
                $entity = $player->getLevel()->getEntity($packet->trData->getEntityRuntimeId());
                $item = $packet->trData->getItemInHand()->getItemStack();

                if($entity instanceof EntityInteractable){
                    /** If a player interacts with entity with a item */
                    $entity->onInteract(new EntityInteractResult($player, $item, null, $packet->trData->getClickPos()));
                    if($entity instanceof ArmorStandEntity){
                        $this->armorStandItemsQueue[$player->getName()] = $packet->trData->getHotbarSlot();
                    }
                }
                if($item instanceof EntityInteractable){
                    /** If a player interacts with entity with a item that has EntityInteractable trait */
                    $item->onInteract(new EntityInteractResult($player, null, $entity));
                }
            }
        }
        if($player->isOp()){
            if($packet instanceof SetPlayerGameTypePacket){
                $player->setGamemode($packet->gamemode);
            }elseif($packet instanceof SetDefaultGameTypePacket){
                Server::getInstance()->setConfigInt("gamemode", $packet->gamemode);
            }elseif($packet instanceof SetDifficultyPacket){
                $player->getLevel()->setDifficulty($packet->difficulty);
            }
        }

        /** Fixes trade gui not opening for second time bug */
        if($packet instanceof ContainerClosePacket && $packet->windowId === 255){
            $player->dataPacket($packet);
        }
    }

    public function onDamage(EntityDamageEvent $event): void{
        $entity = $event->getEntity();

        VanillaX::getInstance()->getEnchantmentManager()->handleDamage($event);
        if(!$event->isCancelled()){
            switch($event->getCause()){
                case EntityDamageEvent::CAUSE_FALL:
                    if(GameRule::getGameRuleValue(GameRule::FALL_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    if($entity instanceof Player){
                        $session = VanillaX::getInstance()->getSessionManager()->get($entity);

                        if($session->isGliding()){
                            $event->setCancelled();
                        }else{
                            if(($end = $session->getEndGlideTime()) !== null && ($start = $session->getStartGlideTime()) !== null){
                                if(($end - $start) < 3){
                                    $event->setCancelled();
                                }
                            }
                        }
                    }
                    break;
                case EntityDamageEvent::CAUSE_DROWNING:
                    if(GameRule::getGameRuleValue(GameRule::DROWNING_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
                case EntityDamageEvent::CAUSE_FIRE:
                    if(GameRule::getGameRuleValue(GameRule::FIRE_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
            }
        }
        if(!$event->isCancelled() && $event instanceof EntityDamageByEntityEvent && $entity instanceof Player){
            if(!GameRule::getGameRuleValue(GameRule::PVP, $entity->getLevel())){
                $event->setCancelled();
                return;
            }
            $item = $entity->getInventory()->getItemInHand();

            if($item instanceof ShieldItem && $entity->isSneaking()){
                $damager = $event->getDamager();

                if($damager instanceof Living && $damager->getDirection() !== $entity->getDirection()){
                    $event->setCancelled();
                    $item->applyDamage(1);
                    $entity->getInventory()->setItemInHand($item);
                    $entity->getLevel()->broadcastLevelSoundEvent($entity, LevelSoundEventPacket::SOUND_ITEM_SHIELD_BLOCK);
                    $damager->knockBack($entity, 0, $damager->x - $entity->x, $damager->z - $entity->z, 0.5);
                }
            }
        }
    }

    public function onTransaction(InventoryTransactionEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleInventoryTransaction($event);
    }

    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        $weather = VanillaX::getInstance()->getWeatherManager();

        GameRule::fixGameRule($player);
        if($weather->isRaining($player->getLevel())) $weather->sendWeather($player, $weather->isThundering($player->getLevel()));
    }

    public function onQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        $manager = VanillaX::getInstance()->getSessionManager();
        $session = $manager->get($player);

        foreach($session->getThrownTridents() as $entity){
            if($entity->isAlive() && !$entity->isFlaggedForDespawn()){
                $entity->onCollideWithPlayer($player);
            }
        }
        $manager->remove($player);
    }

    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $item = $event->getItem();

        if(!$event->isCancelled() && ($slot = ItemManager::getArmorSlot($item, true)) !== null){
            if($player->getArmorInventory()->getItem($slot)->isNull()){
                if(isset($this->armorStandItemsQueue[$player->getName()])){
                    $slot = $this->armorStandItemsQueue[$player->getName()];

                    if($item->equalsExact($player->getInventory()->getHotbarSlotItem($slot))){
                        unset($this->armorStandItemsQueue[$player->getName()]);
                        $event->setCancelled();
                        return;
                    }
                }
                $player->getArmorInventory()->setItem($slot, $item);
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event): void{
        $player = $event->getPlayer();

        if($player->getInventory()->getItemInHand() instanceof ShieldItem){
            $player->setGenericFlag(Entity::DATA_FLAG_BLOCKING, $event->isSneaking());
        }
    }

    public function onAnvilLandFall(EntityBlockChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();

            if($entity instanceof FallingBlock && ($to = $event->getTo())->getId() === BlockIds::ANVIL){
                $pk = Session::playSound($to->asVector3(), "random.anvil_land", 1, 1, true);
                $to->getLevel()->broadcastPacketToViewers($to, $pk);
            }
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();
            $previous = $event->getOrigin();
            $target = $event->getTarget();
            $weather = VanillaX::getInstance()->getWeatherManager();
            $previousWeather = $weather->getWeather($previous);
            $targetWeather = $weather->getWeather($target);

            if($entity instanceof Player){
                GameRule::fixGameRule($entity, $target);
                if($previousWeather->isRaining() && !$targetWeather->isRaining()){
                    $weather->sendClear($entity);
                }
            }
        }
    }

    public function onLevelLoad(LevelLoadEvent $event): void{
        VanillaX::getInstance()->getWeatherManager()->addWeather($event->getLevel());
    }

    public function onLevelUnload(LevelUnloadEvent $event): void{
        if(!$event->isCancelled()){
            VanillaX::getInstance()->getWeatherManager()->removeWeather($event->getLevel());
        }
    }

    public function onBreak(BlockBreakEvent $event): void{
        if(!$event->isCancelled() && !GameRule::getGameRuleValue(GameRule::DO_TILE_DROPS, $event->getBlock()->getLevel())){
            $event->setDrops([]);
        }
    }

    public function onDeath(PlayerDeathEvent $event): void{
        if(!GameRule::getGameRuleValue(GameRule::KEEP_INVENTORY, ($level = $event->getPlayer()->getLevel()))){
            $event->setKeepInventory(true);
        }
        if(!GameRule::getGameRuleValue(GameRule::SHOW_DEATH_MESSAGES, $level)){
            $event->setDeathMessage("");
        }
    }

    public function onRegenerateHealth(EntityRegainHealthEvent $event): void{
        if(!$event->isCancelled() && !GameRule::getGameRuleValue(GameRule::NATURAL_REGENERATION, $event->getEntity()->getLevel())){
            $event->setCancelled();
        }
    }

    public function onEntitySpawn(EntitySpawnEvent $event): void{
        $entity = $event->getEntity();

        if($entity instanceof PrimedTNT && !GameRule::getGameRuleValue(GameRule::TNT_EXPLODES, $entity->getLevel())){
            $entity->flagForDespawn();
        }
    }

    public function onBedLeave(PlayerBedLeaveEvent $event): void{
        $player = $event->getPlayer();

        if(count(Server::getInstance()->getOnlinePlayers()) === 1 && $player->getLevel()->getTime() === Level::TIME_FULL){
            $weather = VanillaX::getInstance()->getWeatherManager()->getWeather($player->getLevel());
            $weather->stopStorm();
        }
    }
}
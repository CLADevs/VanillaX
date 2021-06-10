<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\level\Level;
use pocketmine\Server;

class PlayerListener implements Listener{

    private ListenerManager $manager;

    public function __construct(ListenerManager $manager){
        $this->manager = $manager;
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
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $item = $event->getItem();

            if(($slot = ItemManager::getArmorSlot($item, true)) !== null && $player->getArmorInventory()->getItem($slot)->isNull()){
                if(isset($this->armorStandItemsQueue[$player->getName()])){
                    $slot = $this->manager->armorStandItemsQueue[$player->getName()];

                    if($item->equalsExact($player->getInventory()->getHotbarSlotItem($slot))){
                        unset($this->manager->armorStandItemsQueue[$player->getName()]);
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

    public function onDeath(PlayerDeathEvent $event): void{
        if(!GameRule::getGameRuleValue(GameRule::KEEP_INVENTORY, ($level = $event->getPlayer()->getLevel()))){
            $event->setKeepInventory(true);
        }
        if(!GameRule::getGameRuleValue(GameRule::SHOW_DEATH_MESSAGES, $level)){
            $event->setDeathMessage("");
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
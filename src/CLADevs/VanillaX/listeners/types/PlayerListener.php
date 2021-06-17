<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\inventories\types\OffhandInventory;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\block\StillWater;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
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
        VanillaX::getInstance()->getSessionManager()->add($player);
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
        $player = $event->getPlayer();

        if(!GameRule::getGameRuleValue(GameRule::KEEP_INVENTORY, ($level = $player->getLevel()))){
            $event->setKeepInventory(true);
        }else{
            $offhand = VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory();
            $event->setDrops(array_merge($event->getDrops(), [$offhand->getContents()]));
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

    public function onMove(PlayerMoveEvent $event): void{
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $from = $event->getFrom();
            $to = $event->getTo();
            $item = $player->getArmorInventory()->getBoots();
            
            if($item->hasEnchantment(Enchantment::FROST_WALKER) && !$player->isOnGround() && (intval($to->x) !== intval($from->x) || intval($to->y) !== intval($from->y) || intval($to->z) !== intval($from->z))){
                $block = $player->getLevel()->getBlock($player);
                $aboveBlock = $player->getLevel()->getBlock($player->add(0, 1));

                if($block->getId() === BlockIds::AIR && $aboveBlock->getId() === BlockIds::AIR){
                    $belowBlock = $player->getLevel()->getBlock($player->subtract(0, 1));

                    if($belowBlock instanceof StillWater){
                        $size = 2 + min($item->getEnchantmentLevel(Enchantment::FROST_WALKER), 2);
                        $ice = BlockFactory::get(BlockIds::FROSTED_ICE, 0);

                        for($x = intval($player->x) - $size; $x <= intval($player->x) + $size; $x++){
                            for($z = intval($player->z) - $size; $z <= intval($player->z) + $size; $z++){
                                $pos = new Vector3($x, intval($player->y - 1), $z);

                                if(in_array($player->getLevel()->getBlock($pos)->getId(), [BlockIds::AIR, BlockIds::STILL_WATER, BlockIds::FROSTED_ICE])){
                                    $player->getLevel()->setBlock($pos, $ice, true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function onSave(PlayerDataSaveEvent $event): void{
        $playerName = $event->getPlayerName();

        if(!$event->isCancelled()){
            $offhand = VanillaX::getInstance()->getSessionManager()->get($playerName)->getOffHandInventory();
            $nbt = $event->getSaveData();
            $nbt->setTag($offhand->getItem(0)->nbtSerialize(-1, OffhandInventory::TAG_OFF_HAND_ITEM));
            $event->setSaveData($nbt);
        }
        VanillaX::getInstance()->getSessionManager()->remove($playerName);
    }
}
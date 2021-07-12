<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\items\types\ElytraItem;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\utils\item\HeldItemChangeTrait;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
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
        $session->setTradingEntity(null, true);
        $manager->remove($player);
    }

    public function onInteract(PlayerInteractEvent $event): void{
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $item = $event->getItem();

            if(($slot = ItemManager::getArmorSlot($item, true)) !== null && $player->getArmorInventory()->getItem($slot)->isNull()){
                $player->getArmorInventory()->setItem($slot, $item);
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }
    }

    public function onBlockPick(PlayerBlockPickEvent $event): void{
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $result = $event->getResultItem();
            $freeIndex = null;
            $existIndex = null;

            for($i = 0; $i <= 8; $i++){
                if($inventory->isSlotEmpty($i) && $freeIndex === null){
                    $freeIndex = $i;
                }elseif($inventory->getItem($i)->equals($result)){
                    $existIndex = $i;
                    break;
                }
            }
            if($existIndex !== null){
                $inventory->setHeldItemIndex($existIndex);
                return;
            }
            if(!$inventory->getItemInHand()->isNull() && $freeIndex !== null){
                $event->setCancelled();
                $inventory->setItem($freeIndex, $event->getResultItem());
            }
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event): void{
        $player = $event->getPlayer();
        $offhandItem = VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory()->getItem(0);

        if($player->getInventory()->getItemInHand() instanceof ShieldItem || $offhandItem instanceof ShieldItem){
            VanillaX::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($player): void{
                if($player->isOnline()) $player->setGenericFlag(Entity::DATA_FLAG_BLOCKING, $player->isSneaking());
            }), 5);
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
            $chestplate = $player->getArmorInventory()->getChestplate();
            $boots = $player->getArmorInventory()->getBoots();

            if($boots->hasEnchantment(Enchantment::FROST_WALKER) && !$player->isOnGround() && (intval($to->x) !== intval($from->x) || intval($to->y) !== intval($from->y) || intval($to->z) !== intval($from->z))){
                $block = $player->getLevel()->getBlock($player);
                $aboveBlock = $player->getLevel()->getBlock($player->add(0, 1));

                if($block->getId() === BlockLegacyIds::AIR && $aboveBlock->getId() === BlockLegacyIds::AIR){
                    $belowBlock = $player->getLevel()->getBlock($player->subtract(0, 1));

                    if($belowBlock instanceof StillWater){
                        $size = 2 + min($boots->getEnchantmentLevel(Enchantment::FROST_WALKER), 2);
                        $ice = BlockFactory::get(BlockLegacyIds::FROSTED_ICE, 0);

                        for($x = intval($player->x) - $size; $x <= intval($player->x) + $size; $x++){
                            for($z = intval($player->z) - $size; $z <= intval($player->z) + $size; $z++){
                                $pos = new Vector3($x, intval($player->y - 1), $z);

                                if(in_array($player->getLevel()->getBlock($pos)->getId(), [BlockLegacyIds::AIR, BlockLegacyIds::STILL_WATER, BlockLegacyIds::FROSTED_ICE])){
                                    $player->getLevel()->setBlock($pos, $ice, true);
                                }
                            }
                        }
                    }
                }
            }
            if($chestplate instanceof ElytraItem){
                $session = VanillaX::getInstance()->getSessionManager()->get($player);

                if($session->isGliding()){
                    if(Server::getInstance()->getTick() % 20 == 0){
                        $chestplate->applyDamage(1);
                        $player->getArmorInventory()->setChestplate($chestplate);
                    }
                    if($player->pitch >= -40 && $player->pitch <= 30){
                        $player->resetFallDistance();
                    }
                }
            }
        }
    }

    public function onChangeSlot(PlayerItemHeldEvent $event): void{
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $session = VanillaX::getInstance()->getSessionManager()->get($player);
            $entity = $session->getRidingEntity();
            $oldItem = $player->getInventory()->getItemInHand();
            $newItem = $event->getItem();

            if($entity instanceof HeldItemChangeTrait){
                $entity->onSlotChange($player, $oldItem, $newItem);
            }
            if($newItem instanceof HeldItemChangeTrait){
                $newItem->onSlotChange($player, $oldItem, $newItem);
            }
        }
    }
}

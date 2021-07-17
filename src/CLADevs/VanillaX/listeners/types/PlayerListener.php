<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\items\types\ElytraItem;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\utils\item\HeldItemChangeTrait;
use CLADevs\VanillaX\VanillaX;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Water;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
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
use pocketmine\item\Armor;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\World;

class PlayerListener implements Listener{

    private ListenerManager $manager;

    public function __construct(ListenerManager $manager){
        $this->manager = $manager;
    }

    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        $weather = WeatherManager::getInstance();

        GameRuleManager::getInstance()->sendChanges($player);
        if($weather->isRaining($player->getWorld())) $weather->sendWeather($player, $weather->isThundering($player->getWorld()));
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

            if($item instanceof Armor && $player->getArmorInventory()->getItem($slot = $item->getArmorSlot())->isNull()){
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
                $event->cancel();
                $inventory->setItem($freeIndex, $event->getResultItem());
            }
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event): void{
        $player = $event->getPlayer();
        $offhandItem = $player->getOffHandInventory()->getItem(0);

        if($player->getInventory()->getItemInHand() instanceof ShieldItem || $offhandItem instanceof ShieldItem){
            VanillaX::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($player): void{
                if($player->isOnline()) $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::BLOCKED_USING_SHIELD, $player->isSneaking());
            }), 5);
        }
    }

    public function onDeath(PlayerDeathEvent $event): void{
        $player = $event->getPlayer();

        if(!GameRuleManager::getInstance()->getValue(GameRule::KEEP_INVENTORY, ($level = $player->getWorld()))){
            $event->setKeepInventory(true);
        }
        if(!GameRuleManager::getInstance()->getValue(GameRule::SHOW_DEATH_MESSAGES, $level)){
            $event->setDeathMessage("");
        }
    }

    public function onBedLeave(PlayerBedLeaveEvent $event): void{
        $player = $event->getPlayer();

        if(count(Server::getInstance()->getOnlinePlayers()) === 1 && $player->getWorld()->getTime() === World::TIME_FULL){
            WeatherManager::getInstance()->getWeather($player->getWorld())->stopStorm();
        }
    }

    public function onMove(PlayerMoveEvent $event): void{
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            $from = $event->getFrom();
            $to = $event->getTo();
            $chestplate = $player->getArmorInventory()->getChestplate();
            $boots = $player->getArmorInventory()->getBoots();

            if($boots->hasEnchantment($enchantment = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FROST_WALKER)) && !$player->isOnGround() && (intval($to->x) !== intval($from->x) || intval($to->y) !== intval($from->y) || intval($to->z) !== intval($from->z))){
                $block = $player->getWorld()->getBlock($player->getPosition());
                $aboveBlock = $player->getWorld()->getBlock($player->getPosition()->add(0, 1, 0));

                if($block->getId() === BlockLegacyIds::AIR && $aboveBlock->getId() === BlockLegacyIds::AIR){
                    $belowBlock = $player->getWorld()->getBlock($player->getPosition()->subtract(0, 1, 0));

                    if($belowBlock instanceof Water){
                        $size = 2 + min($boots->getEnchantmentLevel($enchantment), 2);
                        $ice = BlockFactory::getInstance()->get(BlockLegacyIds::FROSTED_ICE, 0);

                        for($x = intval($player->getPosition()->x) - $size; $x <= intval($player->getPosition()->x) + $size; $x++){
                            for($z = intval($player->getPosition()->z) - $size; $z <= intval($player->getPosition()->z) + $size; $z++){
                                $pos = new Vector3($x, intval($player->getPosition()->y - 1), $z);

                                if(in_array($player->getWorld()->getBlock($pos)->getId(), [BlockLegacyIds::AIR, BlockLegacyIds::STILL_WATER, BlockLegacyIds::FROSTED_ICE])){
                                    $player->getWorld()->setBlock($pos, $ice, true);
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
                    if($player->getLocation()->pitch >= -40 && $player->getLocation()->pitch <= 30){
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

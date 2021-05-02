<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\session\Session;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Player;
use pocketmine\Server;

class ArmorStandEntity extends Living implements EntityInteractable{

    const NETWORK_ID = self::ARMOR_STAND;

    const EQUIPMENT_MAINHAND = 0;
    const EQUIPMENT_OFFHAND = 1;
    const EQUIPMENT_HEAD = 2;
    const EQUIPMENT_CHEST = 3;
    const EQUIPMENT_LEGS = 4;
    const EQUIPMENT_FEETS = 5;

    const POSE_DEFAULT = 0;
    const POSE_NO = 1;
    const POSE_SOLEMN = 2;
    const POSE_ATHENA = 3;
    const POSE_BRANDISH = 4;
    const POSE_HONOR = 5;
    const POSE_ENTERTAIN = 6;
    const POSE_SALUTE = 7;
    const POSE_HERO = 8;
    const POSE_RIPOSTE = 9;
    const POSE_ZOMBIE = 10;
    const POSE_CANCAN_A = 11;
    const POSE_CANCAN_B = 12;

    const TAG_ARMOR = "ArmorItem";
    const TAG_MAINHAND = "MainHandItem";
    const TAG_OFFHAND = "OffHandItem";

    public $width = 0.5;
    public $height = 1.975;
    protected $gravity = 0.5;

    private Item $mainHand;
    private Item $offHand;

    private float $lastPunchTime = 0.0;

    protected function initEntity(): void{
        parent::initEntity();
        /** Hands */
        if($this->namedtag->hasTag(self::TAG_MAINHAND, CompoundTag::class)){
            $this->setMainHand(Item::nbtDeserialize($this->namedtag->getCompoundTag(self::TAG_MAINHAND)));
        }else{
            $this->mainHand = ItemFactory::get(ItemIds::AIR);
        }
        if($this->namedtag->hasTag(self::TAG_OFFHAND, CompoundTag::class)){
            $this->setOffHand(Item::nbtDeserialize($this->namedtag->getCompoundTag(self::TAG_OFFHAND)));
        }else{
            $this->offHand = ItemFactory::get(ItemIds::AIR);
        }

        /** Armor */
        $inventoryTag = $this->namedtag->getListTag(self::TAG_ARMOR);
        if($inventoryTag !== null){
            $armorListener = $this->armorInventory->getEventProcessor();
            $this->armorInventory->setEventProcessor(null);

            /** @var CompoundTag $item */
            foreach($inventoryTag as $i => $item){
                $this->armorInventory->setItem($item->getByte("Slot"), Item::nbtDeserialize($item));
            }
            $this->armorInventory->setEventProcessor($armorListener);
        }
    }

    public function getName(): string{
        return "ArmorStand";
    }

    public function getMainHand(): Item{
        return $this->mainHand;
    }

    public function setMainHand(Item $mainHand): void{
        $this->mainHand = $mainHand;
        Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $this->getHandPacket());
    }

    public function getOffHand(): Item{
        return $this->offHand;
    }

    public function setOffHand(Item $offHand): void{
        $this->offHand = $offHand;
        Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $this->getHandPacket(null, true));
    }

    public function getHandPacket(Item $item = null, bool $offhand = false): MobEquipmentPacket{
        if($item === null){
            $item = $offhand ? $this->offHand : $this->mainHand;
        }
        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->id;
        $pk->item = ItemStackWrapper::legacy($item);
        $pk->inventorySlot = $offhand ? 1 : 0;
        $pk->hotbarSlot = $pk->inventorySlot;
        return $pk;
    }

    public function killArmorStand(): void{
        if(!$this->isFlaggedForDespawn()){
            $this->flagForDespawn();
            $items = array_merge([ItemFactory::get(ItemIds::ARMOR_STAND)], $this->getArmorInventory()->getContents());
            if(!$this->mainHand->isNull()){
                $items[] = $this->mainHand;
            }
            if(!$this->offHand->isNull()){
                $items[] = $this->offHand;
            }
            foreach($items as $item){
                $this->getLevel()->dropItem($this->add(0.5, 0.5, 0.5), $item);
            }
        }
    }

    public function getPose(): int{
        return $this->getDataPropertyManager()->getInt(self::DATA_ARMOR_STAND_POSE_INDEX) ?? self::POSE_DEFAULT;
    }

    public function setPose(int $pose, bool $strict = true): void{
        if($strict && ($pose > self::POSE_CANCAN_B || $pose < self::POSE_DEFAULT)){
            $pose = self::POSE_DEFAULT;
        }
        $this->getDataPropertyManager()->setInt(self::DATA_ARMOR_STAND_POSE_INDEX, $pose);
    }

    public function saveNBT(): void{
        /** Armor */
        $inventoryTag = new ListTag(self::TAG_ARMOR, [], NBT::TAG_Compound);
        $this->namedtag->setTag($inventoryTag);
        for($slot = 0; $slot < 4; ++$slot){
            $item = $this->armorInventory->getItem($slot);
            if(!$item->isNull()){
                $inventoryTag->push($item->nbtSerialize($slot));
            }
        }

        /** Hands */
        $this->namedtag->setTag($this->mainHand->nbtSerialize(-1, self::TAG_MAINHAND));
        $this->namedtag->setTag($this->offHand->nbtSerialize(-1, self::TAG_OFFHAND));
        parent::saveNBT();
    }

    protected function sendSpawnPacket(Player $player): void{
        parent::sendSpawnPacket($player);
        $player->dataPacket($this->getHandPacket());
        $player->dataPacket($this->getHandPacket(null, true));
    }

    public function attack(EntityDamageEvent $source): void{
        if($source instanceof EntityDamageByEntityEvent && !$this->isFlaggedForDespawn()){
            $damager = $source->getDamager();

            if($damager instanceof Player){
                if($damager->isCreative()){
                    $this->flagForDespawn();
                }else{
                    $newHealth = $this->getHealth() - $source->getFinalDamage();

                    if($newHealth < 1){
                        $this->killArmorStand();
                    }else{
                        $time = time() - $this->lastPunchTime;

                        if($time >= 0.5 && $time < 1){
                            Session::playSound($damager, "mob.armor_stand.break");
                            $this->killArmorStand();
                        }else{
                            Session::playSound($damager, "mob.armor_stand.hit");
                            $this->setHealth($newHealth);
                            $this->lastPunchTime = time();
                        }
                    }
                }
            }
        }
    }

    public function onInteract(EntityInteractResult $result): void{
        $player = $result->getPlayer();
        $item = $result->getItem();
        $CacheAddItem = null;
        $CacheRemoveItem = null;
        $takeItem = true;

        if($player->isSneaking()){
            $this->setPose($this->getPose() + 1);
           return;
        }
        if($item instanceof Armor){
            $slot = ItemManager::getArmorSlot($item);

            if($slot !== null){
                $slotItem = $this->getArmorInventory()->getItem($slot);

                if(!$slotItem->isNull()){
                    if($slotItem->equals($item)) return;
                    $CacheAddItem = $slotItem;
                }
                $CacheRemoveItem = $item;
                $this->getArmorInventory()->setItem($slot, $item);
                $takeItem = false;
            }
        }else{
            if($item->getId() !== ItemIds::ARMOR_STAND && !$item->isNull() && !$this->mainHand->equals($item)){
                if(!$this->mainHand->isNull()){
                    $CacheAddItem = $this->mainHand;
                }
                $CacheRemoveItem = $item;
                $this->setMainHand($item);
                $takeItem = false;
            }
        }
        if($CacheRemoveItem instanceof Item){
            if($CacheAddItem instanceof Item){
                $player->getInventory()->addItem($CacheAddItem);
            }
            $item = $player->getInventory()->getItemInHand();
            if(!$item->isNull()){
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }
        if($takeItem){
            $addItem = null;
            $slot = $this->getClickedPosSlot($result->getClickPos()->y - $this->y);

            switch($slot){
                case self::EQUIPMENT_MAINHAND:
                    if(!$this->mainHand->isNull()){
                        $addItem = $this->getMainHand();
                        $this->setMainHand(ItemFactory::get(ItemIds::AIR));
                    }
                    break;
                case self::EQUIPMENT_OFFHAND:
                    if(!$this->offHand->isNull()){
                        $addItem = $this->getOffHand();
                        $this->setOffHand(ItemFactory::get(ItemIds::AIR));
                    }
                    break;
                case self::EQUIPMENT_HEAD:
                    $addItem = $this->armorInventory->getHelmet();
                    $this->armorInventory->setHelmet(ItemFactory::get(ItemIds::AIR));
                    break;
                case self::EQUIPMENT_CHEST:
                    $addItem = $this->armorInventory->getChestplate();
                    $this->armorInventory->setChestplate(ItemFactory::get(ItemIds::AIR));
                    break;
                case self::EQUIPMENT_LEGS:
                    $addItem = $this->armorInventory->getLeggings();
                    $this->armorInventory->setLeggings(ItemFactory::get(ItemIds::AIR));
                    break;
                case self::EQUIPMENT_FEETS:
                    $addItem = $this->armorInventory->getBoots();
                    $this->armorInventory->setBoots(ItemFactory::get(ItemIds::AIR));
                    break;
            }
            if($addItem !== null && !$addItem->isNull()){
                if($player->getInventory()->canAddItem($addItem)){
                    $player->getInventory()->addItem($addItem);
                }else{
                    $player->getLevel()->dropItem($player, $addItem);
                }
            }
        }
    }

    public function getClickedPosSlot(float $i): int{
        //TODO Offhand, i havent seen armorstand do offhand in vanilla
        if($i >= 1.6 && !$this->armorInventory->getHelmet()->isNull()){
            return self::EQUIPMENT_HEAD;
        }elseif(($i >= 0.4 && $i < 1.2) && !$this->armorInventory->getChestplate()->isNull()){
            return self::EQUIPMENT_CHEST;
        }elseif(($i >= 0.9 && $i < 1.6) && !$this->armorInventory->getLeggings()->isNull()){
            return self::EQUIPMENT_LEGS;
        }elseif(($i >= 0.1 && $i < 0.55) && !$this->armorInventory->getBoots()->isNull()){
            return self::EQUIPMENT_FEETS;
        }
        return self::EQUIPMENT_MAINHAND;
    }
}
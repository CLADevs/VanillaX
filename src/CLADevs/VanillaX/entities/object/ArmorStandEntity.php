<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\Server;

class ArmorStandEntity extends Living implements InteractButtonItemTrait{

    const BUTTON_EQUIP = "Equip";
    const BUTTON_POSE = "Pose";

    const NETWORK_ID = self::ARMOR_STAND;

    const EQUIPMENT_MAINHAND = 0;
    const EQUIPMENT_HEAD = 1;
    const EQUIPMENT_CHEST = 2;
    const EQUIPMENT_LEGS = 3;
    const EQUIPMENT_FEETS = 4;

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

    private float $lastPunchTime = 0.0;

    protected function initEntity(): void{
        parent::initEntity();
        /** Main Hand */
        if($this->namedtag->hasTag(self::TAG_MAINHAND, CompoundTag::class)){
            $this->setMainHand(Item::nbtDeserialize($this->namedtag->getCompoundTag(self::TAG_MAINHAND)));
        }else{
            $this->mainHand = ItemFactory::getInstance()->get(ItemIds::AIR);
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

        /** Main Hand */
        $this->namedtag->setTag($this->mainHand->nbtSerialize(-1, self::TAG_MAINHAND));
        parent::saveNBT();
    }

    protected function sendSpawnPacket(Player $player): void{
        parent::sendSpawnPacket($player);
        $player->dataPacket($this->getMainHandPacket());
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

    public function killArmorStand(): void{
        if(!$this->isFlaggedForDespawn()){
            $this->flagForDespawn();

            if(GameRule::getGameRuleValue(GameRule::DO_TILE_DROPS, $this->getLevel())){
                $items = array_merge([ItemFactory::getInstance()->get(ItemIds::ARMOR_STAND)], $this->getArmorInventory()->getContents());
                if(!$this->mainHand->isNull()){
                    $items[] = $this->mainHand;
                }
                foreach($items as $item){
                    $this->getLevel()->dropItem($this->add(0.5, 0.5, 0.5), $item);
                }
            }
        }
    }

    public function onMouseHover(Player $player): void{
        $player->getDataPropertyManager()->setString(self::DATA_INTERACTIVE_TAG, $player->isSneaking() ? self::BUTTON_POSE : self::BUTTON_EQUIP);
    }

    public function onButtonPressed(InteractButtonResult $result): void{
        if(!$this->getArmorInventory() instanceof ArmorInventory){
            return;
        }
        $result->setInteractQueue(false);
        $player = $result->getPlayer();
        $item = $result->getItem();
        $button = $result->getButton();

        if($button !== null && strlen($button) < 1){
            $button = $player->isSneaking() ? self::BUTTON_POSE : self::BUTTON_EQUIP;
        }
        switch($button){
            case self::BUTTON_EQUIP:
                if(($clickpos = $result->getClickPos()) !== null){
                    $slot = $this->getClickedPosSlot($clickpos->y - $this->y);
                }else{
                    $slot = $item instanceof Armor ? ItemManager::getArmorSlot($item, true) + 1 : self::EQUIPMENT_MAINHAND;
                }

                /** Remove Item From Armor Stand */
                $slotItem = $slot === self::EQUIPMENT_MAINHAND ? $this->getMainHand() : $this->getArmorInventory()->getItem($slot - 1);

                if($slotItem->isNull()){
                    $takeSlot = null;

                    /**
                     * @var int $key
                     * @var Item $value */
                    foreach(array_merge($this->getArmorInventory()->getContents(true), [5 => $this->getMainHand()]) as $key => $value){
                        if(!$value->isNull()){
                            $slot = $key === 5 ? self::EQUIPMENT_MAINHAND : $key + 1;
                            $slotItem = $slot === self::EQUIPMENT_MAINHAND ? $this->getMainHand() : $this->getArmorInventory()->getItem($slot - 1);
                            break;
                        }
                    }
                }
                if($item->getId() === ItemIds::AIR && !$slotItem->isNull()){
                    if($slot === self::EQUIPMENT_MAINHAND){
                        $this->handleMainHandItem($player, $slotItem, $item);
                    }else{
                        $this->handleArmorItem($player, $slotItem, $item, $slot - 1);
                    }
                    return;
                }

                /** Add Item to Armor Stand */
                if($item instanceof Armor){
                    $slot = ItemManager::getArmorSlot($item, true);
                    $this->handleArmorItem($player, $this->getArmorInventory()->getItem($slot), $item, $slot);
                }elseif($item->getId() !== ItemIds::AIR){
                    $this->handleMainHandItem($player, $this->getMainHand(), $item);
                }
                $this->onMouseHover($player);
                break;
            case self::BUTTON_POSE:
                if($player->isSneaking()){
                    $this->setPose($this->getPose() + 1);
                    return;
                }
                $this->onMouseHover($player);
                break;
        }
    }

    public function handleMainHandItem(Player $player, Item $old, Item $new): void{
        if($new->getId() === ItemIds::ARMOR_STAND){
            return;
        }
        if(!$old->isNull()){
            $player->getInventory()->setItemInHand($old);
        }else{
            $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(ItemIds::AIR));
        }
        $this->setMainHand($new);
        Session::playSound($player, "mob.armor_stand.place");
    }

    public function handleArmorItem(Player $player, Item $old, Item $new, int $slot): void{
        if(!$old->isNull()){
            $player->getInventory()->setItemInHand($old);
        }else{
            $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(ItemIds::AIR));
        }
        $this->getArmorInventory()->setItem($slot, $new);
        Session::playSound($player, "mob.armor_stand.place");
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

    public function getMainHand(): Item{
        return $this->mainHand;
    }

    public function setMainHand(Item $mainHand): void{
        $this->mainHand = $mainHand;
        Server::getInstance()->broadcastPacket($this->getViewers(), $this->getMainHandPacket());
    }

    public function getMainHandPacket(Item $item = null): MobEquipmentPacket{
        if($item === null){
            $item = $this->mainHand;
        }
        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->id;
        $pk->item = ItemStackWrapper::legacy($item);
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = $pk->inventorySlot;
        return $pk;
    }

    public function getClickedPosSlot(float $i): int{
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
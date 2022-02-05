<?php

namespace CLADevs\VanillaX\enchantments;

use CLADevs\VanillaX\configuration\features\EnchantmentFeature;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\utils\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\player\Player;
use pocketmine\item\enchantment\ItemFlags as PMItemFlags;

class EnchantmentManager{

    /** @var Enchantment[] */
    private array $enchantmentMap = [];
    /** @var Enchantment[] */
    private array $enchantmentTypeMap = [];

    public function startup(): void{
        Utils::callDirectory("enchantments", function (string $namespace): void{
            if(in_array(EnchantmentTrait::class, class_uses($namespace), true)){
                $this->registerEnchantment(new $namespace());
            }
        });
    }

    public function registerEnchantment(Enchantment $enchantment): void{
        if(EnchantmentFeature::getInstance()->isEnchantmentEnabled($enchantment->getId())){
            EnchantmentIdMap::getInstance()->register($enchantment->getMcpeId(), $enchantment);
            /** @var EnchantmentTrait $enchantment */
            $this->enchantmentMap[$enchantment->getId()] = $enchantment;
            $this->enchantmentTypeMap[$enchantment->getPrimaryItemFlags()][] = $enchantment;
            $this->enchantmentTypeMap[$enchantment->getSecondaryItemFlags()][] = $enchantment;
        }
    }

    public function handleInventoryTransaction(InventoryTransactionEvent $event): void{
        if(!$event->isCancelled()){
            $tr = $event->getTransaction();
            $player = $tr->getSource();

            foreach($tr->getActions() as $act){
                if($act instanceof SlotChangeAction){
                    $source = $act->getSourceItem();
                    $inv = $act->getInventory();

                    if(EnchantmentFeature::getInstance()->isEnchantmentEnabled("binding") && !$player->isCreative() && $source->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::BINDING)) && ($inv instanceof PlayerInventory || $inv instanceof ArmorInventory)){
                        $event->cancel();
                    }
                }
            }
        }
    }

    public function handleDamage(EntityDamageEvent $event): void{
        if(!$event->isCancelled()){
            if($event instanceof EntityDamageByEntityEvent){
                $entity = $event->getEntity();
                $damager = $event->getDamager();

                if($damager instanceof Player && $entity instanceof VanillaEntity){
                    $item = $damager->getInventory()->getItemInHand();

                    /** Bane of Arthropods  */
                    if($item->hasEnchantment($enchantment = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::BANE_OF_ARTHROPODS)) && $entity->getClassification() === EntityClassification::ARTHROPODS){
                        $level = $item->getEnchantmentLevel($enchantment);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));

                        $duration = mt_rand(10, 15) / 10;
                        $duration += $level > 1 ? (0.5 * $level) : 0;
                        $entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * $duration, 4));
                    }

                    /** Smite  */
                    if($item->hasEnchantment($enchantment = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::SMITE)) && $entity->getClassification() === EntityClassification::UNDEAD){
                        $level = $item->getEnchantmentLevel($enchantment);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));
                    }
                }
            }
        }
    }

    /**
     * @param Item $item
     * @param bool $includeGlobal
     * @param bool $includeTreasures
     * @return Enchantment[]|null
     */
    public function getEnchantmentForItem(Item $item, bool $includeGlobal = true, bool $includeTreasures = true): ?array{
        $enchantments = [];

        /** Armor */
        if($item instanceof Armor){
            $enchantments = $this->enchantmentTypeMap[PMItemFlags::ARMOR] ?? [];

            $flag = match($item->getArmorSlot()){
                ArmorInventory::SLOT_HEAD => PMItemFlags::HEAD,
                ArmorInventory::SLOT_CHEST => PMItemFlags::TORSO,
                ArmorInventory::SLOT_LEGS => PMItemFlags::LEGS,
                ArmorInventory::SLOT_FEET => PMItemFlags::FEET,
                default => null,
            };
            if($flag !== null){
                $enchantments = array_merge($enchantments, $this->enchantmentTypeMap[$flag] ?? []);
            }
        }
        /** Sword or Axe */
        if($item instanceof Axe){
            $enchantments = $this->enchantmentTypeMap[PMItemFlags::AXE] ?? [];
        }
        if($item instanceof Sword){
            $enchantments = $this->enchantmentTypeMap[PMItemFlags::SWORD] ?? [];
        }
        /** Pickaxe, Axe or Shove */
        if($item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel){
            $enchantments = array_merge($enchantments, $this->enchantmentTypeMap[PMItemFlags::DIG] ?? []);
        }
        /** Bow, Crossbow, Trident and FishingRod */
        switch($item->getId()){
            case ItemIds::BOW:
                $enchantments = $this->enchantmentTypeMap[PMItemFlags::BOW] ?? [];
                break;
            case ItemIds::CROSSBOW:
                $enchantments = $this->enchantmentTypeMap[ItemFlags::CROSSBOW] ?? [];
                break;
            case ItemIds::TRIDENT:
                $enchantments = $this->enchantmentTypeMap[PMItemFlags::TRIDENT] ?? [];
                break;
            case ItemIds::FISHING_ROD:
                $enchantments = $this->enchantmentTypeMap[PMItemFlags::FISHING_ROD] ?? [];
                break;
        }
        if($includeGlobal){
            $enchantments = array_merge($this->enchantmentTypeMap[PMItemFlags::ALL] ?? [], $enchantments);
        }
        /** @var EnchantmentTrait $enchantment */
        foreach($enchantments as $key => $enchantment){
            if(!$includeTreasures && $enchantment->isTreasure()){
                unset($enchantment[$key]);
            }
        }
        if(count($enchantments) < 1) return null;
        return $enchantments;
    }

    public function getAllEnchantments(bool $includeTreasure = true): array{
        $enchantments = $this->enchantmentMap;

        /** @var EnchantmentTrait $enchant */
        foreach($enchantments as $key => $enchant){
            if(!$includeTreasure && $enchant->isTreasure()){
                unset($enchantments[$key]);
            }
        }
        return $enchantments;
    }

    /**
     * @return Enchantment[]
     */
    public function getEnchantmentMap(): array{
        return $this->enchantmentMap;
    }

    /**
     * @return Enchantment[]
     */
    public function getEnchantmentTypeMap(): array{
        return $this->enchantmentTypeMap;
    }
}

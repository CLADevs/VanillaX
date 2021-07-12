<?php

namespace CLADevs\VanillaX\enchantments;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
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

class EnchantmentManager{

    /** @var Enchantment[] */
    private array $enchantments = [];

    public function startup(): void{
        Utils::callDirectory("enchantments", function (string $namespace): void{
            if(in_array(EnchantmentTrait::class, class_uses($namespace), true)){
                $this->registerEnchantment(new $namespace());
            }
        });
        //TODO Crossbow enchantment
    }

    public function registerEnchantment(Enchantment $enchantment): void{
        $this->enchantments[$enchantment->getRuntimeId()] = $enchantment;
        if(!in_array($enchantment->getRuntimeId(), VanillaX::getInstance()->getConfig()->getNested("disabled.enchantments", []))){
            EnchantmentIdMap::getInstance()->register($enchantment->getRuntimeId(), $enchantment);
        }
    }

    /**
     * @return int[]
     */
    public function getTreasureEnchantsId(): array{
        return [EnchantmentIds::FROST_WALKER, EnchantmentIds::BINDING, EnchantmentIds::SOUL_SPEED, EnchantmentIds::MENDING, EnchantmentIds::VANISHING];
    }

    /**
     * @return int[]
     */
    public function getGlobalEnchantsId(): array{
        return [EnchantmentIds::VANISHING, EnchantmentIds::UNBREAKING, EnchantmentIds::MENDING];
    }

    /**
     * @return int[]
     */
    public function getWeaponEnchantsId(): array{
        return [EnchantmentIds::BANE_OF_ARTHROPODS, EnchantmentIds::SHARPNESS, EnchantmentIds::SMITE];
    }

    /**
     * @return int[]
     */
    public function getToolEnchantsId(): array{
        return [EnchantmentIds::EFFICIENCY, EnchantmentIds::FORTUNE, EnchantmentIds::SILK_TOUCH];
    }

    /**
     * @return int[]
     */
    public function getArmorEnchantsId(): array{
        return [EnchantmentIds::PROTECTION, EnchantmentIds::BLAST_PROTECTION, EnchantmentIds::FIRE_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION];
    }

    /**
     * @return int[]
     */
    public function getHelmetEnchantsId(): array{
        return [EnchantmentIds::AQUA_AFFINITY, EnchantmentIds::RESPIRATION];
    }

    /**
     * @return int[]
     */
    public function getBootEnchantsId(): array{
        return [EnchantmentIds::DEPTH_STRIDER, EnchantmentIds::FEATHER_FALLING, EnchantmentIds::FROST_WALKER, EnchantmentIds::SOUL_SPEED];
    }

    /**
     * @return int[]
     */
    public function getSwordEncantsId(): array{
        return [EnchantmentIds::FIRE_ASPECT, EnchantmentIds::KNOCKBACK, EnchantmentIds::LOOTING];
    }

    /**
     * @return int[]
     */
    public function getElytraEnchantsId(): array{
        return [EnchantmentIds::BINDING];
    }

    /**
     * @return int[]
     */
    public function getBowEnchantsId(): array{
        return [EnchantmentIds::FLAME, EnchantmentIds::INFINITY, EnchantmentIds::PUNCH];
    }
    /**
     * @return int[]
     */
    public function getCrossbowEnchantsId(): array{
        return [EnchantmentIds::MULTISHOT, EnchantmentIds::PIERCING, EnchantmentIds::QUICK_CHARGE];
    }

    /**
     * @return int[]
     */
    public function getTridentEnchantsId(): array{
        return [EnchantmentIds::CHANNELING, EnchantmentIds::IMPALING, EnchantmentIds::LOYALTY, EnchantmentIds::RIPTIDE];
    }

    /**
     * @return int[]
     */
    public function getFishingRodEnchantsId(): array{
        return [EnchantmentIds::LUCK_OF_THE_SEA, EnchantmentIds::LURE];
    }

    /**
     * @return Enchantment[]
     */
    public function getEnchantments(): array{
        return $this->enchantments;
    }

    public function getAllEnchantments(bool $treasure = false): array{
        $treasure = $treasure ? $this->getTreasureEnchantsId() : [];
        return array_merge($this->getGlobalEnchantsId(), $this->getWeaponEnchantsId(), $this->getToolEnchantsId(), $this->getArmorEnchantsId(), $this->getHelmetEnchantsId(), $this->getBootEnchantsId(), $this->getSwordEncantsId(), $this->getElytraEnchantsId(), $this->getBowEnchantsId(), $this->getCrossbowEnchantsId(), $this->getTridentEnchantsId(), $this->getFishingRodEnchantsId(), $treasure);
    }

    public function getEnchantmentForItem(Item $item, bool $includeGlobal = true, bool $includeTreasures = true): ?array{
        $enchantments = [];

        /** Armor */
        if($item instanceof Armor){
            $enchantments = $this->getArmorEnchantsId();
        }
        /** Sword or Axe */
        if($item instanceof Sword || $item instanceof Axe){
            $enchantments = $this->getWeaponEnchantsId();

            if($item instanceof Sword){
                $enchantments = array_merge($enchantments, $this->getSwordEncantsId());
            }
        }
        /** Pickaxe, Axe or Shove */
        if($item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel){
            $enchantments = $this->getToolEnchantsId();
        }
        /** Helmet, Boots, Elytra, Bow, Crossbow, Trident and FishingRod */
        switch($item->getId()){
            case ItemIds::LEATHER_HELMET:
            case ItemIds::CHAIN_HELMET:
            case ItemIds::GOLD_HELMET:
            case ItemIds::IRON_HELMET:
            case ItemIds::DIAMOND_HELMET:
                $enchantments = array_merge($enchantments, $this->getHelmetEnchantsId());
                break;
            case ItemIds::LEATHER_BOOTS:
            case ItemIds::CHAIN_BOOTS:
            case ItemIds::GOLD_BOOTS:
            case ItemIds::IRON_BOOTS:
            case ItemIds::DIAMOND_BOOTS:
                $enchantments = array_merge($enchantments, $this->getBootEnchantsId());
                break;
            case ItemIds::ELYTRA:
                if($includeTreasures){
                    $enchantments = array_merge($enchantments, $this->getElytraEnchantsId());
                }
                break;
            case ItemIds::BOW:
                $enchantments = $this->getBowEnchantsId();
                break;
            case ItemIds::CROSSBOW:
                $enchantments = $this->getCrossbowEnchantsId();
                break;
            case ItemIds::TRIDENT:
                $enchantments = $this->getTridentEnchantsId();
                break;
            case ItemIds::FISHING_ROD:
                $enchantments = $this->getFishingRodEnchantsId();
                if(!$includeTreasures){
                    unset($enchantments[EnchantmentIds::FROST_WALKER]);
                    unset($enchantments[EnchantmentIds::SOUL_SPEED]);
                }
                break;
        }
        if(count($enchantments) < 1) return null;
        if($includeGlobal){
            $global = $this->getGlobalEnchantsId();
            unset($global[EnchantmentIds::MENDING]);
            unset($global[EnchantmentIds::VANISHING]);
            return array_merge($global, $enchantments);
        }
        return $enchantments;
    }

    public function handleInventoryTransaction(InventoryTransactionEvent $event): void{
        if(!$event->isCancelled()){
            $tr = $event->getTransaction();
            $player = $tr->getSource();

            foreach($tr->getActions() as $act){
                if($act instanceof SlotChangeAction){
                    $source = $act->getSourceItem();
                    $inv = $act->getInventory();

                    if(!$player->isCreative() && $source->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::BINDING)) && ($inv instanceof PlayerInventory || $inv instanceof ArmorInventory)){
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
}

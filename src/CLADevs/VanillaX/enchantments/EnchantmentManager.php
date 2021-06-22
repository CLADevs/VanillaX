<?php

namespace CLADevs\VanillaX\enchantments;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\Player;

class EnchantmentManager{

    /** @var Enchantment[] */
    private array $enchantments = [];

    public function startup(): void{
        Utils::callDirectory("enchantments", function (string $namespace): void{
            if(in_array(EnchantmentTrait::class, class_uses($namespace), true)){
                self::registerEnchantment(new $namespace());
            }
        });
        //TODO Crossbow enchantment
    }

    public function registerEnchantment(Enchantment $enchantment): void{
        $this->enchantments[$enchantment->getId()] = $enchantment;
        if(!in_array($enchantment->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.enchantments", []))){
            Enchantment::registerEnchantment($enchantment);
        }
    }

    /**
     * @return int[]
     */
    public function getTreasureEnchantsId(): array{
        return [Enchantment::FROST_WALKER, Enchantment::BINDING, Enchantment::SOUL_SPEED, Enchantment::MENDING, Enchantment::VANISHING];
    }

    /**
     * @return int[]
     */
    public function getGlobalEnchantsId(): array{
        return [Enchantment::VANISHING, Enchantment::UNBREAKING, Enchantment::MENDING];
    }

    /**
     * @return int[]
     */
    public function getWeaponEnchantsId(): array{
        return [Enchantment::BANE_OF_ARTHROPODS, Enchantment::SHARPNESS, Enchantment::SMITE];
    }

    /**
     * @return int[]
     */
    public function getToolEnchantsId(): array{
        return [Enchantment::EFFICIENCY, Enchantment::FORTUNE, Enchantment::SILK_TOUCH];
    }

    /**
     * @return int[]
     */
    public function getArmorEnchantsId(): array{
        return [Enchantment::PROTECTION, Enchantment::BLAST_PROTECTION, Enchantment::FIRE_PROTECTION, Enchantment::PROJECTILE_PROTECTION];
    }

    /**
     * @return int[]
     */
    public function getHelmetEnchantsId(): array{
        return [Enchantment::AQUA_AFFINITY, Enchantment::RESPIRATION];
    }

    /**
     * @return int[]
     */
    public function getBootEnchantsId(): array{
        return [Enchantment::DEPTH_STRIDER, Enchantment::FEATHER_FALLING, Enchantment::FROST_WALKER, Enchantment::SOUL_SPEED];
    }

    /**
     * @return int[]
     */
    public function getSwordEncantsId(): array{
        return [Enchantment::FIRE_ASPECT, Enchantment::KNOCKBACK, Enchantment::LOOTING];
    }

    /**
     * @return int[]
     */
    public function getElytraEnchantsId(): array{
        return [Enchantment::BINDING];
    }

    /**
     * @return int[]
     */
    public function getBowEnchantsId(): array{
        return [Enchantment::FLAME, Enchantment::INFINITY, Enchantment::PUNCH];
    }
    /**
     * @return int[]
     */
    public function getCrossbowEnchantsId(): array{
        return [Enchantment::MULTISHOT, Enchantment::PIERCING, Enchantment::QUICK_CHARGE];
    }

    /**
     * @return int[]
     */
    public function getTridentEnchantsId(): array{
        return [Enchantment::CHANNELING, Enchantment::IMPALING, Enchantment::LOYALTY, Enchantment::RIPTIDE];
    }

    /**
     * @return int[]
     */
    public function getFishingRodEnchantsId(): array{
        return [Enchantment::LUCK_OF_THE_SEA, Enchantment::LURE];
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
        $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getEnchantments();

        /** Armor */
        if($item instanceof Armor){
            $enchantments = $this->getArmorEnchantsId();
        }
        /** Sword or Axe */
        if($enchantments === null && ($item instanceof Sword || $item instanceof Axe)){
            $enchantments = $this->getWeaponEnchantsId();

            if($item instanceof Sword){
                $enchantments = array_merge($enchantments, $this->getSwordEncantsId());
            }
        }
        /** Pickaxe, Axe or Shove */
        if($enchantments === null && ($item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel)){
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
                    unset($enchantments[Enchantment::FROST_WALKER]);
                    unset($enchantments[Enchantment::SOUL_SPEED]);
                }
                break;
        }
        if($enchantments === null) return null;
        if($includeGlobal){
            $global = $this->getGlobalEnchantsId();
            unset($global[Enchantment::MENDING]);
            unset($global[Enchantment::VANISHING]);
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

                    if(!$player->isCreative() && $source->hasEnchantment(Enchantment::BINDING) && ($inv instanceof PlayerInventory || $inv instanceof ArmorInventory)){
                        $event->setCancelled();
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
                    if($item->hasEnchantment(Enchantment::BANE_OF_ARTHROPODS) && $entity->getClassification() === EntityClassification::ARTHROPODS){
                        $level = $item->getEnchantmentLevel(Enchantment::BANE_OF_ARTHROPODS);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));

                        $duration = mt_rand(10, 15) / 10;
                        $duration += $level > 1 ? (0.5 * $level) : 0;
                        $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20 * $duration, 4));
                    }

                    /** Smite  */
                    if($item->hasEnchantment(Enchantment::SMITE) && $entity->getClassification() === EntityClassification::UNDEAD){
                        $level = $item->getEnchantmentLevel(Enchantment::SMITE);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));
                    }
                }
            }
        }
    }
}

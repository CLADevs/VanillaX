<?php

namespace CLADevs\VanillaX\enchantments;

use CLADevs\VanillaX\entities\LivingEntity;
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

    public static array $treasure = [Enchantment::FROST_WALKER, Enchantment::BINDING, Enchantment::SOUL_SPEED, Enchantment::MENDING, Enchantment::VANISHING];
    public static array $global = [Enchantment::VANISHING, Enchantment::UNBREAKING, Enchantment::MENDING];
    public static array $weapon = [Enchantment::BANE_OF_ARTHROPODS, Enchantment::SHARPNESS, Enchantment::SMITE];
    public static array $tools = [Enchantment::EFFICIENCY, Enchantment::FORTUNE, Enchantment::SILK_TOUCH];
    public static array $armors = [Enchantment::PROTECTION, Enchantment::BLAST_PROTECTION, Enchantment::FIRE_PROTECTION, Enchantment::PROJECTILE_PROTECTION];
    public static array $helmet = [Enchantment::AQUA_AFFINITY, Enchantment::RESPIRATION];
    public static array $boots = [Enchantment::DEPTH_STRIDER, Enchantment::FEATHER_FALLING, Enchantment::FROST_WALKER, Enchantment::SOUL_SPEED];
    public static array $sword = [Enchantment::FIRE_ASPECT, Enchantment::KNOCKBACK, Enchantment::LOOTING];
    public static array $elytra = [Enchantment::BINDING];
    public static array $bow = [Enchantment::FLAME, Enchantment::INFINITY, Enchantment::PUNCH];
    public static array $crossbow = [Enchantment::MULTISHOT, Enchantment::PIERCING, Enchantment::QUICK_CHARGE];
    public static array $trident = [Enchantment::CHANNELING, Enchantment::IMPALING, Enchantment::LOYALTY, Enchantment::RIPTIDE];
    public static array $fishingRod = [Enchantment::LUCK_OF_THE_SEA, Enchantment::LURE];

    public function startup(): void{
        self::registerEnchantment(new Enchantment(Enchantment::AQUA_AFFINITY, "Aqua Affinity", Enchantment::RARITY_RARE, Enchantment::SLOT_HEAD, Enchantment::SLOT_NONE, 1));
        self::registerEnchantment(new Enchantment(Enchantment::BANE_OF_ARTHROPODS, "Bane of Arthropods", Enchantment::RARITY_RARE, Enchantment::SLOT_SWORD, Enchantment::SLOT_AXE, 5));
        self::registerEnchantment(new Enchantment(Enchantment::SMITE, "Smite", Enchantment::RARITY_RARE, Enchantment::SLOT_SWORD, Enchantment::SLOT_AXE, 5));
        self::registerEnchantment(new Enchantment(Enchantment::BINDING, "Curse of Binding", Enchantment::RARITY_RARE, Enchantment::SLOT_ARMOR, Enchantment::SLOT_ELYTRA, 1));
        self::registerEnchantment(new Enchantment(Enchantment::CHANNELING, "Channeling", Enchantment::RARITY_RARE, Enchantment::SLOT_TRIDENT, Enchantment::SLOT_NONE, 1));
        self::registerEnchantment(new Enchantment(Enchantment::RIPTIDE, "Riptide", Enchantment::RARITY_RARE, Enchantment::SLOT_TRIDENT, Enchantment::SLOT_NONE, 3));
        self::registerEnchantment(new Enchantment(Enchantment::LOYALTY, "Loyalty", Enchantment::RARITY_RARE, Enchantment::SLOT_TRIDENT, Enchantment::SLOT_NONE, 3));
        self::registerEnchantment(new Enchantment(Enchantment::IMPALING, "Impaling", Enchantment::RARITY_RARE, Enchantment::SLOT_TRIDENT, Enchantment::SLOT_NONE, 5));
        self::registerEnchantment(new Enchantment(Enchantment::DEPTH_STRIDER, "Depth Strider", Enchantment::RARITY_RARE, Enchantment::SLOT_FEET, Enchantment::SLOT_NONE, 3));
        Utils::callDirectory("enchantments" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            self::registerEnchantment(new $namespace());
        });
        //TODO Crossbow enchantment
    }

    public function registerEnchantment(Enchantment $enchantment): void{
        if(!in_array($enchantment->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.enchantments", []))){
            Enchantment::registerEnchantment($enchantment);
        }
    }

    /**
     * @param bool $treasure
     * @return int[]
     */
    public static function getAllEnchantments(bool $treasure = false): array{
        $treasure = $treasure ? self::$treasure : [];
        return array_merge(self::$global, self::$weapon, self::$tools, self::$armors, self::$helmet, self::$boots, self::$sword, self::$elytra, self::$bow, self::$crossbow, self::$trident, self::$fishingRod, $treasure);
    }

    public static function getEnchantmentForItem(Item $item, bool $includeGlobal = true, bool $includeTreasures = true): ?array{
        $enchantments = null;

        /** Armor */
        if($item instanceof Armor){
            $enchantments = self::$armors;
        }
        /** Sword or Axe */
        if($enchantments === null && ($item instanceof Sword || $item instanceof Axe)){
            $enchantments = self::$weapon;

            if($item instanceof Sword){
                $enchantments = array_merge($enchantments, self::$sword);
            }
        }
        /** Pickaxe, Axe or Shove */
        if($enchantments === null && ($item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel)){
            $enchantments = self::$tools;
        }
        /** Helmet, Boots, Elytra, Bow, Crossbow, Trident and FishingRod */
        switch($item->getId()){
            case ItemIds::LEATHER_HELMET:
            case ItemIds::CHAIN_HELMET:
            case ItemIds::GOLD_HELMET:
            case ItemIds::IRON_HELMET:
            case ItemIds::DIAMOND_HELMET:
                $enchantments = array_merge($enchantments, self::$helmet);
                break;
            case ItemIds::LEATHER_BOOTS:
            case ItemIds::CHAIN_BOOTS:
            case ItemIds::GOLD_BOOTS:
            case ItemIds::IRON_BOOTS:
            case ItemIds::DIAMOND_BOOTS:
                $enchantments = array_merge($enchantments, self::$boots);
                break;
            case ItemIds::ELYTRA:
                if($includeTreasures){
                    $enchantments = array_merge($enchantments, self::$elytra);
                }
                break;
            case ItemIds::BOW:
                $enchantments = self::$bow;
                break;
            case ItemIds::CROSSBOW:
                $enchantments = self::$crossbow;
                break;
            case ItemIds::TRIDENT:
                $enchantments = self::$trident;
                break;
            case ItemIds::FISHING_ROD:
                $enchantments = self::$fishingRod;
                if(!$includeTreasures){
                    unset($enchantments[Enchantment::FROST_WALKER]);
                    unset($enchantments[Enchantment::SOUL_SPEED]);
                }
                break;
        }
        if($enchantments === null) return null;
        if($includeGlobal){
            $global = self::$global;
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

                if($damager instanceof Player && $entity instanceof LivingEntity){
                    $item = $damager->getInventory()->getItemInHand();

                    /** Bane of Arthropods  */
                    if($item->hasEnchantment(Enchantment::BANE_OF_ARTHROPODS) && isset(LivingEntity::ARTHROPODS[$entity::NETWORK_ID])){
                        $level = $item->getEnchantmentLevel(Enchantment::BANE_OF_ARTHROPODS);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));

                        $duration = mt_rand(10, 15) / 10; //this just gets 1 to 1.5
                        $duration += $level > 1 ? (0.5 * $level) : 0;
                        $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20 * $duration, 4));
                    }

                    /** Smite  */
                    if($item->hasEnchantment(Enchantment::SMITE) && isset(LivingEntity::UNDEAD[$entity::NETWORK_ID])){
                        $level = $item->getEnchantmentLevel(Enchantment::SMITE);
                        $event->setBaseDamage($event->getBaseDamage() + ($level * 2.5));
                    }
                }
            }
        }
    }
}
<?php

namespace CLADevs\VanillaX\enchantments;

use CLADevs\VanillaX\enchantments\types\AquaAffinityEnchantment;
use CLADevs\VanillaX\enchantments\types\BaneofArthropodsEnchantment;
use CLADevs\VanillaX\enchantments\types\BindingEnchantment;
use CLADevs\VanillaX\enchantments\types\ChannelingEnchantment;
use CLADevs\VanillaX\enchantments\types\DepthStriderEnchantment;
use CLADevs\VanillaX\enchantments\types\FortuneEnchantment;
use CLADevs\VanillaX\enchantments\types\FrostWalkerEnchantment;
use CLADevs\VanillaX\enchantments\types\ImpalingEnchantment;
use CLADevs\VanillaX\enchantments\types\LootingEnchantment;
use CLADevs\VanillaX\enchantments\types\LoyaltyEnchantment;
use CLADevs\VanillaX\enchantments\types\LuckOfTheSeaEnchantment;
use CLADevs\VanillaX\enchantments\types\LureEnchantment;
use CLADevs\VanillaX\enchantments\types\RiptideEnchantment;
use CLADevs\VanillaX\enchantments\types\SmiteEnchantment;
use pocketmine\event\Event;
use pocketmine\event\inventory\InventoryTransactionEvent;
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
        Enchantment::registerEnchantment(new DepthStriderEnchantment());
        Enchantment::registerEnchantment(new AquaAffinityEnchantment());
        Enchantment::registerEnchantment(new SmiteEnchantment());
        Enchantment::registerEnchantment(new BaneofArthropodsEnchantment());
        Enchantment::registerEnchantment(new LootingEnchantment());
        Enchantment::registerEnchantment(new FortuneEnchantment());
        Enchantment::registerEnchantment(new LuckOfTheSeaEnchantment());
        Enchantment::registerEnchantment(new LureEnchantment());
        Enchantment::registerEnchantment(new FrostWalkerEnchantment());
        Enchantment::registerEnchantment(new ImpalingEnchantment());
        Enchantment::registerEnchantment(new BindingEnchantment());
        Enchantment::registerEnchantment(new ImpalingEnchantment());
        Enchantment::registerEnchantment(new RiptideEnchantment());
        Enchantment::registerEnchantment(new LoyaltyEnchantment());
        Enchantment::registerEnchantment(new ChannelingEnchantment());
        //TODO Crossbow enchantment
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

    public function handleReceivedEvent(Event $event): void{
        if(!$event->isCancelled()){
            if($event instanceof InventoryTransactionEvent){
                $tr = $event->getTransaction();

                foreach($tr->getActions() as $act){
                    if($act instanceof SlotChangeAction){
                        $source = $act->getSourceItem();
                        $inv = $act->getInventory();

                        if($inv instanceof PlayerInventory && $source->hasEnchantment(Enchantment::BINDING)){
                            $event->setCancelled();
                        }
                    }
                }
            }
        }
    }
}
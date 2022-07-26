<?php

namespace CLADevs\VanillaX\enchantments;

use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Shovel;
use pocketmine\item\Tool;
use pocketmine\item\enchantment\ItemFlags as PMItemFlags;

trait EnchantmentTrait{

    /**
     * @var int
     * i've made it static since you can't make const in traits
     * extending PMMP Enchantment.php wouldnt work due to fact
     * some enchantment such as knockback needs to extend pmmp
     * Knockback class
     */
    public static int $SLOT_CROSSBOW = 0x10000;

    abstract public function getId(): string;
    abstract public function getMcpeId(): int;

    public function isTreasure(): bool{
        return false;
    }

    public function getOptionId(): int{
        foreach([PMItemFlags::ARMOR, PMItemFlags::HEAD, PMItemFlags::TORSO, PMItemFlags::LEGS, PMItemFlags::FEET] as $flag){
            if($this->isItemFlagValid($flag)){
                return self::OPTION_EQUIP;
            }
        }
        foreach([PMItemFlags::SWORD, PMItemFlags::AXE, PMItemFlags::DIG, PMItemFlags::TOOL] as $flag){
            if($this->isItemFlagValid($flag)){
                if(in_array($this->getMcpeId(), [EnchantmentIds::KNOCKBACK, EnchantmentIds::EFFICIENCY])){
                    return self::OPTION_SELF;
                }
                return self::OPTION_HELD;
            }
        }
        foreach([PMItemFlags::FISHING_ROD, PMItemFlags::BOW, PMItemFlags::ALL, PMItemFlags::TRIDENT] as $flag){
            if($this->isItemFlagValid($flag)){
                if(in_array($this->getMcpeId(), [EnchantmentIds::LUCK_OF_THE_SEA, EnchantmentIds::IMPALING, EnchantmentIds::FLAME])){
                    return self::OPTION_HELD;
                }
                return self::OPTION_SELF;
            }
        }
        return self::OPTION_EQUIP;
    }

    public function getRarityCost(): int{
        return match ($this->getRarity()) {
            Rarity::COMMON => RarityCost::COMMON,
            Rarity::UNCOMMON => RarityCost::UNCOMMON,
            Rarity::RARE => RarityCost::RARE,
            Rarity::MYTHIC => RarityCost::MYTHIC,
            default => 0,
        };
    }

    /**
     * @return int[]
     */
    public function getIncompatibles(): array{
        return [];
    }

    public function isIncompatibleWith(VanillaEnchantment $enchantment): bool{
        return in_array($enchantment->getMcpeId(), $this->getIncompatibles());
    }

    /**
     * @param Item $item
     * @return bool
     * default it returns global compatibilities
     */
    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor || $item instanceof Tool || $item instanceof Shovel || in_array($item->getId(), [
                ItemIds::FISHING_ROD, ItemIds::BOW,
                ItemIds::SHEARS, ItemIds::FLINT_AND_STEEL,
                ItemIds::CARROT_ON_A_STICK, ItemIds::SHIELD,
                ItemIds::ELYTRA, ItemIds::TRIDENT,
                ItemIds::CROSSBOW
            ]);
    }

    public function isItemFlagCompatible(Item $item): bool{
        $primary = $this->getPrimaryItemFlags();
        $secondary = $this->getSecondaryItemFlags();

        if($item instanceof Armor){
            return $this->isItemFlagValid(PMItemFlags::ARMOR);
        }
        return false;
    }

    public function isItemFlagValid(int $flag): bool{
        return $this->getPrimaryItemFlags() === $flag || $this->getSecondaryItemFlags() === $flag;
    }
}
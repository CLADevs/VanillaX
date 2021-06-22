<?php

namespace CLADevs\VanillaX\enchantments\utils;

use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Shovel;
use pocketmine\item\Tool;

trait EnchantmentTrait{

    /**
     * @var int
     * i've made it static since you can't make const in traits
     * extending PMMP Enchantment.php wouldnt work due to fact
     * some enchantment such as knockback needs to extend pmmp
     * Knockback class
     */
    public static int $SLOT_CROSSBOW = 0x10000;

    public function isTreasure(): bool{
        return false;
    }

    /**
     * @return int[]
     */
    public function getIncompatibles(): array{
        return [];
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
}
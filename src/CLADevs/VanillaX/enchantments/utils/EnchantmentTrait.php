<?php

namespace CLADevs\VanillaX\enchantments\utils;

use pocketmine\item\enchantment\Enchantment;

trait EnchantmentTrait{

    /**
     * @var int[]
     * Enchantments not visible in enchantment table
     */
    public static array $treasure = [Enchantment::FROST_WALKER, Enchantment::BINDING, Enchantment::SOUL_SPEED, Enchantment::MENDING, Enchantment::VANISHING];

    /**
     * @var int[]
     * Enchantments that can be applied at fishingRod
     */
    public static array $fishingRod = [Enchantment::LUCK_OF_THE_SEA, Enchantment::LURE];


    public function isTreasure(): bool{
        return false;
    }

    /**
     * @return int[]
     */
    public function getIncompatibles(): array{
        return [];
    }
}
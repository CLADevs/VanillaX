<?php

namespace CLADevs\VanillaX\enchantments;

use pocketmine\item\Item;

interface VanillaEnchantment{

    const OPTION_EQUIP = 0;
    const OPTION_HELD = 1;
    const OPTION_SELF = 2;

    public function getPrimaryItemFlags(): int;
    public function getSecondaryItemFlags(): int;
    public function getRarity(): int;
    public function getRarityCost(): int;

    public function getId(): string;
    public function getMcpeId(): int;

    public function isTreasure(): bool;

    /**
     * @return int[]
     */
    public function getIncompatibles(): array;

    public function isIncompatibleWith(VanillaEnchantment $enchantment): bool;

    public function isItemCompatible(Item $item): bool;

    public function getMinCost(int $level): int;
    public function getMaxCost(int $level): int;
    public function getOptionId(): int;

    public function isItemFlagValid(int $flag): bool;
}

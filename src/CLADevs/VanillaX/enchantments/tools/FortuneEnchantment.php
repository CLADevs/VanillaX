<?php

namespace CLADevs\VanillaX\enchantments\tools;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class FortuneEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FORTUNE, "%enchantment.lootBonusDigger", self::RARITY_RARE, self::SLOT_DIG, self::SLOT_NONE, 3);
    }

    public function getIncompatibles(): array{
        return [self::SILK_TOUCH];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool;
    }
}
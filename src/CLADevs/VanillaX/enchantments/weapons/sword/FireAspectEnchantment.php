<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\FireAspectEnchantment as PMFireAspectEnchantment;
use pocketmine\item\Item;
use pocketmine\item\Sword;

class FireAspectEnchantment extends PMFireAspectEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FIRE_ASPECT, "%enchantment.fire", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_NONE, 2);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}
<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;

class FeatherFallingEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FEATHER_FALLING, "%enchantment.protect.fall", self::RARITY_UNCOMMON, self::SLOT_FEET, self::SLOT_NONE, 4, 2.5, [
            EntityDamageEvent::CAUSE_FALL
        ]);
    }

    public function isItemCompatible(Item $item): bool{
        return in_array($item->getId(), ItemManager::getBootsList());
    }
}
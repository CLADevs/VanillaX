<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;

class SoulSpeedEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SOUL_SPEED, "%enchantment.soul_speed", self::RARITY_MYTHIC, self::SLOT_FEET, self::SLOT_NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return in_array($item->getId(), ItemManager::getBootsList());
    }
}
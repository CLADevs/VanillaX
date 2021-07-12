<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\Sword;

class LootingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::LOOTING, "%enchantment.lootBonus", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}
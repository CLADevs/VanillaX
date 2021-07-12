<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\item\Armor;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;

class BindingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::BINDING, "%enchantment.curse.binding", self::RARITY_MYTHIC, self::SLOT_ARMOR, self::SLOT_ELYTRA, 1);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}
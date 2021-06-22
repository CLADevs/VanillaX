<?php

namespace CLADevs\VanillaX\enchantments\armors\helmets;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;

class AquaAffinityEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::AQUA_AFFINITY, "%enchantment.waterWorker", self::RARITY_RARE, self::SLOT_HEAD, self::SLOT_NONE, 1);
    }

    public function isItemCompatible(Item $item): bool{
        return in_array($item->getId(), ItemManager::getHelmetList());
    }
}
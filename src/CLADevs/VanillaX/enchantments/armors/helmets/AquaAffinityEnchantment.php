<?php

namespace CLADevs\VanillaX\enchantments\armors\helmets;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class AquaAffinityEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::AQUA_AFFINITY, "%enchantment.waterWorker", Rarity::RARE, ItemFlags::HEAD, ItemFlags::NONE, 1);
    }

    public function isItemCompatible(Item $item): bool{
        return in_array($item->getId(), ItemManager::getHelmetList());
    }
}
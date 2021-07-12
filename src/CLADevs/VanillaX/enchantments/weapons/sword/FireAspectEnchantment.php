<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\FireAspectEnchantment as PMFireAspectEnchantment;

class FireAspectEnchantment extends PMFireAspectEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::FIRE_ASPECT, "%enchantment.fire", Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 2);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}
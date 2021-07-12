<?php

namespace CLADevs\VanillaX\enchantments\globals;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;

class UnbreakingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::UNBREAKING, "%enchantment.durability", Rarity::UNCOMMON, ItemFlags::DIG | ItemFlags::ARMOR | ItemFlags::FISHING_ROD | ItemFlags::BOW, ItemFlags::TOOL | ItemFlags::CARROT_STICK | ItemFlags::ELYTRA, 3);
    }
}
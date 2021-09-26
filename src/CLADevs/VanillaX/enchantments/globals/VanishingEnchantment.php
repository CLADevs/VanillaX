<?php

namespace CLADevs\VanillaX\enchantments\globals;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\lang\KnownTranslationFactory;

class VanishingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_curse_vanishing(), Rarity::MYTHIC, ItemFlags::NONE, ItemFlags::ALL, 1);
    }

    public function getId(): string{
        return "vanishing";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::VANISHING;
    }

    public function isTreasure(): bool{
        return true;
    }
}
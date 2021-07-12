<?php

namespace CLADevs\VanillaX\enchantments\crossbow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class QuickChargeEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(self::QUICK_CHARGE, "%enchantment.crossbowQuickCharge", self::RARITY_UNCOMMON, self::$SLOT_CROSSBOW, self::SLOT_NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::CROSSBOW;
    }
}
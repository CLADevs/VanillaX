<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use InvalidArgumentException;
use pocketmine\item\Pickaxe;

class NetheritePickaxe extends Pickaxe{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_PICKAXE, 0, "Netherite Pickaxe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float{
        return 10;
    }

    protected static function getBaseDamageFromTier(int $tier): int{
        static $levels = [
            self::TIER_WOODEN => 5,
            self::TIER_GOLD => 5,
            self::TIER_STONE => 6,
            self::TIER_IRON => 7,
            self::TIER_DIAMOND => 8,
            ItemIdentifiers::TIER_NETHERITE => 9
        ];

        if(!isset($levels[$tier])){
            throw new InvalidArgumentException("Unknown tier '$tier'");
        }

        return $levels[$tier];
    }
}
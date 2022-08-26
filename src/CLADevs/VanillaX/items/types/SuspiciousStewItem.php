<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;

class SuspiciousStewItem extends Food{

    public function __construct(){
        parent::__construct(new ItemIdentifier(LegacyItemIds::SUSPICIOUS_STEW, 0), "Suspicious Stew");
    }

    public function getMaxStackSize(): int{
        return 1;
    }

    public function getFoodRestore(): int{
        return 6;
    }

    public function getSaturationRestore(): float{
        return 7.2;
    }

    public function getAdditionalEffects(): array{
        return match ($this->getMeta()){
            0 => [new EffectInstance(VanillaEffects::NIGHT_VISION())],
            1 => [new EffectInstance(VanillaEffects::JUMP_BOOST())],
            2 => [new EffectInstance(VanillaEffects::WEAKNESS())],
            3 => [new EffectInstance(VanillaEffects::BLINDNESS())],
            4 => [new EffectInstance(VanillaEffects::POISON())],
            6 => [new EffectInstance(VanillaEffects::SATURATION())],
            7 => [new EffectInstance(VanillaEffects::FIRE_RESISTANCE())],
            8 => [VanillaEffects::REGENERATION()],
            9 => [VanillaEffects::WITHER()],
            default => [],
        };
    }
}

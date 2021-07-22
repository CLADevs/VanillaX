<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;

class SuspiciousStewItem extends Food{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::SUSPICIOUS_STEW, 0), "Suspicious Stew");
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
        switch($this->getMeta()){
            case 0:
                return [new EffectInstance(VanillaEffects::NIGHT_VISION())];
            case 1:
                return [new EffectInstance(VanillaEffects::JUMP_BOOST())];
            case 2:
                return [new EffectInstance(VanillaEffects::WEAKNESS())];
            case 3:
                return [new EffectInstance(VanillaEffects::BLINDNESS())];
            case 4:
                return [new EffectInstance(VanillaEffects::POISON())];
            case 6:
                return [new EffectInstance(VanillaEffects::SATURATION())];
            case 7:
                return [new EffectInstance(VanillaEffects::FIRE_RESISTANCE())];
            case 8:
                return [VanillaEffects::REGENERATION()];
            case 9:
                return [VanillaEffects::WITHER()];
        }
        return [];
    }
}

<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Food;

class SuspiciousStewItem extends Food{

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::SUSPICIOUS_STEW, $meta, "Suspicious Stew");
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
        switch($this->meta){
            case 0:
                return [new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION))];
            case 1:
                return [new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST))];
            case 2:
                return [new EffectInstance(Effect::getEffect(Effect::WEAKNESS))];
            case 3:
                return [new EffectInstance(Effect::getEffect(Effect::BLINDNESS))];
            case 4:
                return [new EffectInstance(Effect::getEffect(Effect::POISON))];
            case 6:
                return [new EffectInstance(Effect::getEffect(Effect::SATURATION))];
            case 7:
                return [new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE))];
            case 8:
                return [new EffectInstance(Effect::getEffect(Effect::REGENERATION))];
            case 9:
                return [new EffectInstance(Effect::getEffect(Effect::WITHER))];
        }
        return [];
    }
}

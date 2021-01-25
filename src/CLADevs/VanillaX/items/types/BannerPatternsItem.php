<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class BannerPatternsItem extends Item{

    const TYPE_CREEPER_CHARGE = 0;
    const TYPE_SKULL_CHARGE = 1;
    const TYPE_FLOWER_CHARGE = 2;
    const TYPE_THING = 3;
    const TYPE_FIELD_MASONED = 4;
    const TYPE_BORDURE_INDENTED = 5;
    const TYPE_SNOUT = 6;

    public function __construct(int $meta = 0){
        parent::__construct(self::BANNER_PATTERN, $meta, "Banner Pattern");
    }

    public function getType(): int{
        return $this->meta > 6 ? self::TYPE_CREEPER_CHARGE : $this->meta;
    }

    public function getNameForPattern(): string{
        switch($this->getType()){
            case 1:
                return "Skull Charge Banner";
            case 2:
                return "Flower Charge Banner";
            case 3:
                return "Thing Banner";
            case 4:
                return "Field Masoned Banner";
            case 5:
                return "Bordure Indented Banner";
            case 6:
                return "Snout Banner";
        }
        return "Creeper Charge Banner";
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
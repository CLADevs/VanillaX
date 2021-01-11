<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class WitchEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::WITCH;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(26);
    }

    public function getName(): string{
        return "Witch";
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();
        $finalItems = [];

        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::GLASS_BOTTLE, 0, 1);
        }
        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::GLOWSTONE_DUST, 0, 1);
        }
        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::GUNPOWDER, 0, 1);
        }
        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::REDSTONE_DUST, 0, 1);
        }
        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::SPIDER_EYE, 0, 1);
        }
        if($random->nextRange(0, 100) < 12.5){
            $finalItems[] = ItemFactory::get(ItemIds::SUGAR, 0, 1);
        }
        if($random->nextRange(0, 100) < 25){
            $finalItems[] = ItemFactory::get(ItemIds::STICK, 0, 1);
        }
        //TODO Looting
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5;
    }
}
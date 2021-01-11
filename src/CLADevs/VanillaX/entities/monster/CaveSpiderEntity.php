<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CaveSpiderEntity extends LivingEntity{

    public $width = 0.7;
    public $height = 0.5;

    const NETWORK_ID = self::CAVE_SPIDER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(12);
    }

    public function getName(): string{
        return "Cave Spider";
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $string = ItemFactory::get(ItemIds::STRING, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $string->setCount($string->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $string;

        if(mt_rand(1, 3) === 1){
            $spiderEye = ItemFactory::get(ItemIds::SPIDER_EYE, mt_rand(0, 1));
            $spiderEye->setCount($spiderEye->getCount() + mt_rand(0, $looting));
            $finalItems[] = $spiderEye;
        }
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5;
    }
}
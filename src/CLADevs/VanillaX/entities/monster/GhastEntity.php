<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class GhastEntity extends LivingEntity{

    public $width = 4;
    public $height = 4;

    const NETWORK_ID = self::GHAST;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Ghast";
    }

    public function getLootItems(Entity $killer): array{
        $ghastTear = ItemFactory::get(ItemIds::GHAST_TEAR, 0, mt_rand(0, 1));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $ghastTear->setCount($ghastTear->getCount() + mt_rand(0, $looting));
        }
        $gunPowder = ItemFactory::get(ItemIds::GUNPOWDER, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $gunPowder->setCount($gunPowder->getCount() + mt_rand(0, $looting));
        }
        return [$ghastTear, $gunPowder];
    }

    public function getLootExperience(): int{
        return 5;
    }
}
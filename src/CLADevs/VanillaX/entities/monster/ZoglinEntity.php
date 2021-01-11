<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ZoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::ZOGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Zoglin";
    }

    public function getLootItems(Entity $killer): array{
        $rottenFlesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, mt_rand(1, 3));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $rottenFlesh->setCount($rottenFlesh->getCount() + mt_rand(0, $looting));
        }
        return [$rottenFlesh];
    }

    public function getLootExperience(): int{
        return 5 + $this->ageable->isBaby() ? 0 : mt_rand(1, 3);
    }
}
<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ZombiePigmanEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE_PIGMAN;

    public function getName(): string{
        return "Zombie Pigman";
    }

    public function getLootItems(Entity $killer): array{
        $rottenFlesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, mt_rand(0, 1));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $rottenFlesh->setCount($rottenFlesh->getCount() + mt_rand(0, $looting));
        }

        $goldNugget = ItemFactory::get(ItemIds::GOLD_NUGGET, 0, mt_rand(0, 1));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $goldNugget->setCount($goldNugget->getCount() + mt_rand(0, $looting));
        }
        //TODO More drops
        return [$rottenFlesh, $goldNugget];
    }

    public function getLootExperience(): int{
        return $this->ageable->isBaby() ? 12 : 5;
    }
}
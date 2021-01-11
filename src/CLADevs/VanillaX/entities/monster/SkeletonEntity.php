<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SkeletonEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::SKELETON;

    public function getName(): string{
        return "Skeleton";
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $bones = ItemFactory::get(ItemIds::BONE, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $bones->setCount($bones->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $bones;

        $arrows = ItemFactory::get(ItemIds::ARROW, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $arrows->setCount($arrows->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $arrows;
        //TODO more
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5 + (count($this->getArmorInventory()->getContents()) > 0 ? mt_rand(1, 3) : 0);
    }
}
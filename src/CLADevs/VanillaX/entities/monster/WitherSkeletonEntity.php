<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class WitherSkeletonEntity extends LivingEntity{

    public $width = 0.72;
    public $height = 2.01;

    const NETWORK_ID = self::WITHER_SKELETON;

    public function getName(): string{
        return "Wither Skeleton";
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();
        $finalItems = [];

        if($random->nextFloat() < 0.25){
            $finalItems[] = ItemFactory::get(ItemIds::SKULL);
        }
        $bones = ItemFactory::get(ItemIds::BONE, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $bones->setCount($bones->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $bones;

        $coal = ItemFactory::get(ItemIds::COAL, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $coal->setCount($coal->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $coal;
        //TODO more
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5 + (count($this->getArmorInventory()->getContents()) > 0 ? mt_rand(1, 3) : 0);
    }
}
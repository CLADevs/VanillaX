<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class StrayEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::STRAY;

    public function getName(): string{
        return "Stray";
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $bone = ItemFactory::get(ItemIds::BONE, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $bone->setCount($bone->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $bone;

        $arrow = ItemFactory::get(ItemIds::ARROW, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $arrow->setCount($arrow->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $arrow;

        $slowlessArrow = ItemFactory::get(ItemIds::ARROW, 18, mt_rand(0, 1));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $slowlessArrow->setCount($slowlessArrow->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $slowlessArrow;

        //TODO more
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5 + (count($this->getArmorInventory()->getContents()) > 0 ? mt_rand(1, 3) : 0);
    }
}
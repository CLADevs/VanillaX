<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CreeperEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.8;

    const NETWORK_ID = self::CREEPER;

    public function getName(): string{
        return "Creeper";
    }

    public function isCharged(): bool{
        return $this->getGenericFlag(self::DATA_FLAG_POWERED);
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $gunPowder = ItemFactory::get(ItemIds::GUNPOWDER, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $gunPowder->setCount($gunPowder->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $gunPowder;

        if($killer instanceof SkeletonEntity || $killer instanceof StrayEntity){
//            $records = [
//                ItemIds::RECORD_13, ItemIds::RECORD_CAT, ItemIds::RECORD_BLOCKS,
//                ItemIds::RECORD_CHIRP, ItemIds::RECORD_FAR, ItemIds::RECORD_MALL,
//                ItemIds::RECORD_MELLOHI, ItemIds::RECORD_STAL, ItemIds::RECORD_STRAD,
//                ItemIds::RECORD_WARD, ItemIds::RECORD_11, ItemIds::RECORD_WAIT
//            ];
            $finalItems[] = ItemFactory::get(mt_rand(500, 511)); //one random music disc
        }elseif($killer instanceof CreeperEntity && $killer->isCharged()){
            $finalItems[] = ItemFactory::get(ItemIds::MOB_HEAD, 4); //TODO Skeleton, Zombie, etc heads
        }
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 5;
    }
}
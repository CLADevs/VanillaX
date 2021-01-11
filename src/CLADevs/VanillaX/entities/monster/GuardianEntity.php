<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class GuardianEntity extends LivingEntity{

    public $width = 0.85;
    public $height = 0.85;

    const NETWORK_ID = self::GUARDIAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Guardian";
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $shards = ItemFactory::get(ItemIds::PRISMARINE_SHARD, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $shards->setCount($shards->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $shards;

        if(($chance = mt_rand(0, 100)) === 40){
            if(mt_rand(0, 1) === 1){
                $rawFish = ItemFactory::get(ItemIds::FISH, 0, 1);
                if(($looting = $this->getKillerEnchantment($killer)) > 0){
                    $rawFish->setCount($rawFish->getCount() + mt_rand(0, $looting));
                }
                $finalItems[] = $rawFish;
            }else{
                $crystals = ItemFactory::get(ItemIds::PRISMARINE_CRYSTALS, 0, 1);
                if(($looting = $this->getKillerEnchantment($killer)) > 0){
                    $crystals->setCount($crystals->getCount() + mt_rand(0, $looting));
                }
                $finalItems[] = $crystals;
            }
        }
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 10;
    }
}
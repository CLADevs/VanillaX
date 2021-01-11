<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\utils\Random;

class ElderGuardianEntity extends LivingEntity{

    public $width = 1.99;
    public $height = 1.99;

    const NETWORK_ID = self::ELDER_GUARDIAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(80);
    }

    public function getName(): string{
        return "Elder Guardian";
    }

    public function getLootItems(Entity $killer): array{
        $finalItems = [];

        $shards = ItemFactory::get(ItemIds::PRISMARINE_SHARD, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $shards->setCount($shards->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $shards;

        if($killer instanceof Player){
            $finalItems[] = ItemFactory::get(ItemIds::SPONGE); //TODO Wet Sponge
        }
        if($chance = mt_rand(1, 6) !== 6){
            if($chance === 2){
                $rawCod = ItemFactory::get(ItemIds::FISH, 0, 1);

                if($looting > 0){
                    $rawCod->setCount($rawCod->getCount() + mt_rand(0, $looting));
                }
                $finalItems[] = $rawCod;
            }elseif($chance === 3){
                $crystal = ItemFactory::get(ItemIds::PRISMARINE_CRYSTALS, 0, 1);
                if($looting > 0){
                    $crystal->setCount($crystal->getCount() + mt_rand(0, $looting));
                }
                $finalItems[] = $crystal;
            }
        }
        //TODO Random Fish
        return $finalItems;
    }

    public function getLootExperience(): int{
        return 10;
    }
}
<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ZombieEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        //TODO
    }

    public function getName(): string{
        return "Zombie";
    }

    public function getLootItems(Entity $killer): array{
        $rottenFlesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $rottenFlesh->setCount($rottenFlesh->getCount() + mt_rand(0, $looting));
        }
        //TODO More drops
        return [$rottenFlesh];
    }

    public function getLootExperience(): int{
        return 5 + $this->ageable->isBaby() ? 0 : mt_rand(1, 3);
    }
}
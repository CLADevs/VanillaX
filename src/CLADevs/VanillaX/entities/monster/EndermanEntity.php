<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EndermanEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 2.9;

    const NETWORK_ID = self::ENDERMAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Enderman";
    }

    public function getLootItems(Entity $killer): array{
        $enderPearl = ItemFactory::get(ItemIds::ENDER_PEARL, 0, mt_rand(0, 1));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $enderPearl->setCount($enderPearl->getCount() + mt_rand(0, $looting));
        }
        return [$enderPearl];
    }

    public function getLootExperience(): int{
        return 5;
    }
}
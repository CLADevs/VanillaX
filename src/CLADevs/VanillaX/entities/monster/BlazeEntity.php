<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class BlazeEntity extends LivingEntity{

    public $width = 0.5;
    public $height = 1.8;

    const NETWORK_ID = self::BLAZE;

    public function getName(): string{
        return "Blaze";
    }

    public function getLootItems(Entity $killer): array{
        $item = ItemFactory::get(ItemIds::BLAZE_ROD, 0, mt_rand(0, 1));

        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $item->setCount($item->getCount() + mt_rand(0, $looting));
        }
        return [$item];
    }

    public function getLootExperience(): int{
        return 10;
    }
}
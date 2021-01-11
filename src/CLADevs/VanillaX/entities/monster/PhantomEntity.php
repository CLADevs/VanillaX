<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class PhantomEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.5;

    const NETWORK_ID = self::PHANTOM;

    public function getName(): string{
        return "Phantom";
    }

    public function getLootItems(Entity $killer): array{
        $membranes = ItemFactory::get(ItemIds::PHANTOM_MEMBRANE, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $membranes->setCount($membranes->getCount() + mt_rand(0, $looting));
        }
        return [$membranes];
    }

    public function getLootExperience(): int{
        return 5;
    }
}
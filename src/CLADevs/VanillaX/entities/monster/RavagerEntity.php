<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class RavagerEntity extends LivingEntity{

    public $width = 1.9;
    public $height = 1.2;

    const NETWORK_ID = self::RAVAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Ravager";
    }

    public function getLootItems(Entity $killer): array{
        return [ItemFactory::get(ItemIds::SADDLE, 0, 1)];
    }

    public function getLootExperience(): int{
        return 20;
    }
}
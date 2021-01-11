<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class WitherEntity extends LivingEntity{

    public $width = 1;
    public $height = 3;

    const NETWORK_ID = self::WITHER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(600);
    }

    public function getName(): string{
        return "Wither";
    }
    public function getLootItems(Entity $killer): array{
        return [ItemFactory::get(ItemIds::NETHER_STAR, 0, 1)];
    }

    public function getLootExperience(): int{
        return 50;
    }

}
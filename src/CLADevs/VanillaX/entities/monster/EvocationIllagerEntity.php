<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EvocationIllagerEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::EVOCATION_ILLAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Evocation Illager";
    }

    public function getLootItems(Entity $killer): array{
        $totem = ItemFactory::get(ItemIds::TOTEM, 0, 1);
        $emerald = ItemFactory::get(ItemIds::EMERALD, 0, mt_rand(0, 1));
        //TODO illagerBanner
        return [$totem, $emerald];
    }

    public function getLootExperience(): int{
        return 10;
    }
}
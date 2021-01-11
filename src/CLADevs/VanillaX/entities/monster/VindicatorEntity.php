<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class VindicatorEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::VINDICATOR;

    private bool $spawnedNaturallyEquipped = false;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Vindicator";
    }

    public function isSpawnedNaturallyEquipped(): bool{
        return $this->spawnedNaturallyEquipped;
    }

    public function getLootItems(Entity $killer): array{
        return [ItemFactory::get(ItemIds::EMERALD, 0, mt_rand(0, 1))];
    }

    public function getLootExperience(): int{
        return $this->ageable->isBaby() ? 0 : 5 + ($this->spawnedNaturallyEquipped ? mt_rand(1, 3) : 0);
    }
}
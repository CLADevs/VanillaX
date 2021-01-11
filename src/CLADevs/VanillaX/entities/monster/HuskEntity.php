<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HuskEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::HUSK;

    private bool $spawnedNaturallyEquipped = false;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        //TODO
    }

    public function getName(): string{
        return "Husk";
    }

    public function isSpawnedNaturallyEquipped(): bool{
        return $this->spawnedNaturallyEquipped;
    }

    public function getLootItems(Entity $killer): array{
        $rottenFlesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, mt_rand(0, 2));
        return [$rottenFlesh];
    }

    public function getLootExperience(): int{
        return $this->ageable->isBaby() ? 12  : 5 + ($this->spawnedNaturallyEquipped ? mt_rand(1, 3) : 0);
    }
}
<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\Durable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class PillagerEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::PILLAGER;

    private bool $spawnedNaturallyEquipped = false;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Pillager";
    }

    public function isSpawnedNaturallyEquipped(): bool{
        return $this->spawnedNaturallyEquipped;
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();
        $chance = 0.85 + (0.2 * $this->getKillerEnchantment($killer));

        if($random->nextFloat() < $chance){
            $crossbow = ItemFactory::get(ItemIds::CROSSBOW, 0, 1);
            if($crossbow instanceof Durable){
                $crossbow->setDamage(mt_rand(0, $crossbow->getMaxDurability() - 1));
            }
            return [$crossbow];
        }
        return [];
    }

    public function getLootExperience(): int{
        return $this->ageable->isBaby() ? 0 : 5 + ($this->spawnedNaturallyEquipped ? mt_rand(1, 3) : 0);
    }
}
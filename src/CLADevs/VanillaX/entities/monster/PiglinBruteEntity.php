<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\Durable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class PiglinBruteEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::PIGLIN_BRUTE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(50);
    }

    public function getName(): string{
        return "Piglin Brute";
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();

        if($random->nextFloat() < 0.85){
            $axe = ItemFactory::get(ItemIds::GOLDEN_AXE, 0, 1);
            if($axe instanceof Durable){
                $axe->setDamage(mt_rand(0, $axe->getMaxDurability() - 1));
            }
            return [$axe];
        }
        return [];
    }

    public function getLootExperience(): int{
        return 20;
    }
}
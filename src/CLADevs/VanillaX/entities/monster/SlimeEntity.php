<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SlimeEntity extends LivingEntity{

    public $width = 2.08;
    public $height = 2.08;

    const NETWORK_ID = self::SLIME;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Slime";
    }

    public function getLootItems(Entity $killer): array{
        $slimeballs = ItemFactory::get(ItemIds::SLIMEBALL, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $slimeballs->setCount($slimeballs->getCount() + mt_rand(0, $looting));
        }
        return [$slimeballs];
    }

    public function getLootExperience(): int{
        return mt_rand(1, 4); //TODO
    }
}
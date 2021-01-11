<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::HOGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.85, 0.86], [0.9, 0.9]);
        //TODO add Crimson Fungus Item
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Hoglin";
    }

    public function getLootItems(Entity $killer): array{
        $pork = ItemFactory::get(ItemIds::PORKCHOP, 0, mt_rand(2, 4));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $pork->setCount($pork->getCount() + mt_rand(0, $looting));
        }

        $leather = ItemFactory::get(ItemIds::LEATHER, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $leather->setCount($leather->getCount() + mt_rand(0, $looting));
        }
        return [$pork, $leather];
    }

    public function getLootExperience(): int{
        return $this->ageable->isBaby() ? 0 : mt_rand(1, 3);
    }
}
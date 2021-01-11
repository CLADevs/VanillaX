<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\Durable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class PiglinEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::PIGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        //TODO
        $this->ageable = new EntityAgeable($this, [0.6, 1.9], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Piglin";
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();
        $chance = 0.85 + (0.1 * $this->getKillerEnchantment($killer));

        if($random->nextFloat() < $chance){
            $chosen = ItemIds::GOLDEN_SWORD;

            if(mt_rand(0, 1) === 1){
                $chosen = ItemIds::CROSSBOW;
            }
            $weapon = ItemFactory::get($chosen, 0, 1);
            if($weapon instanceof Durable){
                $weapon->setDamage(mt_rand(0, $weapon->getMaxDurability() - 1));
            }
            return [$weapon];
        }
        return [];
    }

    public function getLootExperience(): int{
        if($this->ageable->isBaby()) return 1;
        $points = 5 + mt_rand(1, 3);

        foreach($this->getArmorInventory()->getContents() as $item){
            if(!$item->isNull()){
                $points += mt_rand(1, 3);
            }
        }
        return $points;
    }
}
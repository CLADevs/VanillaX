<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ZombieHorseEntity extends VanillaEntity{

    const NETWORK_ID = self::ZOMBIE_HORSE;

    public $width = 1.4;
    public $height = 1.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Zombie Horse";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rotten_flesh = ItemFactory::getInstance()->get(ItemIds::ROTTEN_FLESH, 0, 1);
        ItemHelper::applySetCount($rotten_flesh, 0, 2);
        ItemHelper::applyLootingEnchant($this, $rotten_flesh);
        return [$rotten_flesh];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}
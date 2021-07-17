<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieHorseEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ZOMBIE_HORSE;

    public float $width = 1.4;
    public float $height = 1.6;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Zombie Horse";
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
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
}
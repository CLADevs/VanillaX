<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PandaEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::PANDA;

    public $width = 1.7;
    public $height = 1.5;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Panda";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO bamboo
//        $bamboo = ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1);
//        ItemHelper::applySetCount($bamboo, 0, 2);
//        ItemHelper::applyLootingEnchant($this, $bamboo);
        return [];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
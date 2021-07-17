<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ZOMBIE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Zombie";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rotten_flesh = ItemFactory::getInstance()->get(ItemIds::ROTTEN_FLESH, 0, 1);
        ItemHelper::applySetCount($rotten_flesh, 0, 2);
        ItemHelper::applyLootingEnchant($this, $rotten_flesh);
        return [$rotten_flesh, ItemFactory::getInstance()->get(ItemIds::IRON_INGOT, 0, 1), ItemFactory::getInstance()->get(ItemIds::CARROT, 0, 1), ItemFactory::getInstance()->get(ItemIds::POTATO, 0, 1)];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1, 3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}
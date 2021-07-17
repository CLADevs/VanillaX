<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PufferfishEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::PUFFERFISH;

    public $width = 0.8;
    public $height = 0.8;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Puffer Fish";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applyLootingEnchant($this, $bone);
        return [ItemFactory::getInstance()->get(ItemIds::PUFFERFISH, 0, 1), $bone];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
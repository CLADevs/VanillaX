<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MooshroomEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::MOOSHROOM;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Mooshroom";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $leather = ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $leather);
         
        $beef = ItemFactory::getInstance()->get(ItemIds::RAW_BEEF, 0, 1);
        ItemHelper::applySetCount($beef, 1, 3);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($beef);
        ItemHelper::applyLootingEnchant($this, $beef);
        return [$leather, $beef];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
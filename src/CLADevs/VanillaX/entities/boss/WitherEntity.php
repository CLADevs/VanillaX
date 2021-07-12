<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::WITHER;

    public float $width = 1;
    public float $height = 3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(600);
    }

    public function getName(): string{
        return "Wither";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
    
    public function getXpDropAmount(): int{
        return 50;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        return [ItemFactory::getInstance()->get(ItemIds::NETHER_STAR, 0, 1)];
    }
}
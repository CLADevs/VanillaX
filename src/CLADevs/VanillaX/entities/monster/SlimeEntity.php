<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SlimeEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SLIME;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        
    }

    public function getName(): string{
        return "Slime";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? self::NETWORK_ID : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $slime_ball = ItemFactory::getInstance()->get(ItemIds::SLIME_BALL, 0, 1);
        ItemHelper::applySetCount($slime_ball, 0, 2);
        ItemHelper::applyLootingEnchant($this, $slime_ball);
        return [$slime_ball];
    }
}
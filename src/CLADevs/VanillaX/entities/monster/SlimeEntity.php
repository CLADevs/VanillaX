<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SlimeEntity extends VanillaEntity{

    const TYPE_LARGE = 0;
    const TYPE_MEDIUM = 1;
    const TYPE_SMALL = 2;

    const NETWORK_ID = self::SLIME;

    public $width = 2.08;
    public $height = 2.08;

    public int $type = self::TYPE_LARGE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->initializeType($this->type);
    }

    protected function initializeType(int $type): void{
        $health = 16;
        $size = 2.08;

        if($type === self::TYPE_MEDIUM){
            $health = 4;
            $size = 0.78;
        }elseif($type === self::TYPE_SMALL){
            $health = 1;
            $size = 0.52;
        }
        $this->width = $size;
        $this->height = $size;
        $this->recalculateBoundingBox();
        $this->setMaxHealth($health);
    }

    public function getName(): string{
        return "Slime";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $slime_ball = ItemFactory::get(ItemIds::SLIME_BALL, 0, 1);
        ItemHelper::applySetCount($slime_ball, 0, 2);
        ItemHelper::applyLootingEnchant($this, $slime_ball);
        return [$slime_ball];
    }
    
    public function getXpDropAmount(): int{
        if($this->getLastHitByPlayer()){
            switch($this->type){
                case self::TYPE_LARGE:
                    return 4;
                case self::TYPE_MEDIUM:
                    return 2;
                case self::TYPE_SMALL:
                    return 1;
            }
        }
        return 0;
    }
}
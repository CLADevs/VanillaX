<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CatEntity extends VanillaEntity{

    const NETWORK_ID = self::CAT;

    public $width = 0.6;
    public $height = 0.7;
    
    public bool $isWild = true;

    protected function initEntity(): void{
        parent::initEntity();
        $this->initializeHealth();
    }

    protected function initializeHealth(): void{
        if($this->isWild){
            $health = 10;
        }else{
            $health = 20;
        }
        $this->setMaxHealth($health);
    }

    public function getName(): string{
        return "Cat";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 0, 2);
        return [$string];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}
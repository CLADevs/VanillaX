<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class MagmaCubeEntity extends LivingEntity{

    const TYPE_LARGE = 0;
    const TYPE_SMALL = 1;
    const TYPE_TINY = 2;

    public $width = 2.08;
    public $height = 2.08;

    const NETWORK_ID = self::MAGMA_CUBE;

    private int $type = self::TYPE_LARGE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Magma Cube";
    }

    public function getLootItems(Entity $killer): array{
        if($this->type === self::TYPE_LARGE){
            return [ItemFactory::get(ItemIds::MAGMA_CREAM, 0, mt_rand(0, 1))];
        }elseif($this->type === self::TYPE_SMALL){
            return [ItemFactory::get(ItemIds::MAGMA_CREAM, 0, mt_rand(0, 1))];
        }else{
            return [];
        }
    }

    public function getLootExperience(): int{
        if($this->type === self::TYPE_LARGE){
            return 4;
        }elseif($this->type === self::TYPE_SMALL){
            return 2;
        }else{
            return 1;
        }
    }
}
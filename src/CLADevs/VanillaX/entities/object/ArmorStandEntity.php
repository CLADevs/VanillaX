<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use pocketmine\entity\Living;

class ArmorStandEntity extends Living implements EntityInteractable{

    public $width = 0.5;
    public $height = 1.975;
    protected $gravity = 0.5;

    const NETWORK_ID = self::ARMOR_STAND;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(1);
        $this->setHealth(1);
    }

    public function getName(): string{
        return "ArmorStand";
    }

    public function onInteract(EntityInteractResult $result): void{
//        $item = $result->getItem();
//
//        if($item instanceof Armor){
//            $slot = ItemManager::getArmorSlot($item);
//
//            if($slot !== null && $this->getArmorInventory()->getItem($slot)->isNull()){
//                $this->getArmorInventory()->setItem($slot, $item);
//            }
//        }
    }
}
<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\item\Item;
use pocketmine\Player;

class HorseArmorItem extends Item implements EntityInteractable, NonAutomaticCallItemTrait{

    public function __construct(int $id, int $meta = 0, string $name = "Unknown"){
        switch($id){
            case self::LEATHER_HORSE_ARMOR:
                $name = "Leather Horse Armor";
                break;
            case self::IRON_HORSE_ARMOR:
                $name = "Iron Horse Armor";
                break;
            case self::GOLD_HORSE_ARMOR:
                $name = "Gold Horse Armor";
                break;
            case self::DIAMOND_HORSE_ARMOR:
                $name = "Diamond Horse Armor";
                break;
        }
        parent::__construct($id, $meta, $name);
    }

    public function onInteractWithEntity(Player $player, Entity $entity): void{
        if($entity->getId() === EntityIds::HORSE){
            //TODO
        }
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
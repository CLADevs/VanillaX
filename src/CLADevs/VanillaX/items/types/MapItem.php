<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\HeldItemChangeTrait;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class MapItem extends Item implements NonAutomaticCallItemTrait, HeldItemChangeTrait, InteractButtonItemTrait{

    const MAP_EMPTY = ItemIds::EMPTY_MAP;
    const MAP_FILLED = ItemIds::FILLED_MAP;

    public function __construct(int $id){
        parent::__construct($id, 0, $id === self::MAP_EMPTY ? "Empty Map" : "Map");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        return true;
    }

    public function isEmptyMap(): bool{
        return $this->id === self::MAP_EMPTY;
    }

    public function onSlotChange(Player $player, Item $old, Item $new): void{
        if($this->isEmptyMap()){
            $player->getDataPropertyManager()->setString(Entity::DATA_INTERACTIVE_TAG, "Create Map");
        }
    }

    public function onButtonPressed(InteractButtonResult $result): void{
        if($this->isEmptyMap()){
            //TODO
        }
    }

    public function onMouseHover(Player $player): void{
    }
}
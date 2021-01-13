<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Armor;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TurtleHelmetItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(self::TURTLE_HELMET, $meta, "Turtle Helmet");
    }

    public function getMaxStackSize(): int{
        return 1;
    }

    public function getMaxDurability(): int{
        return 276;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        if($player->getArmorInventory()->getHelmet()->isNull()){
            $player->getArmorInventory()->setHelmet($this);
            if(!$player->isSurvival() || !$player->isAdventure()) $this->pop();
        }
        return true;
    }
}
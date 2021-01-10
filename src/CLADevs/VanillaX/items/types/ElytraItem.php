<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ElytraItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(self::ELYTRA, $meta, "Elytra");
    }

    public function getMaxStackSize(): int{
        return 1;
    }

    public function getMaxDurability(): int{
        return 432;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        if($player->getArmorInventory()->getChestplate()->isNull()){
            $player->getArmorInventory()->setChestplate($this);
            $this->pop();
        }
        return true;
    }
}
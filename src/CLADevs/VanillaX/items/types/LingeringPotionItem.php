<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LingeringPotionItem extends ProjectileItem{

    public function __construct(int $meta = 0){
        parent::__construct(self::LINGERING_POTION, $meta, "Lingering Potion");
    }

    public function getThrowForce(): float{
        return 0.5;
    }

    public function getProjectileEntityType(): string{
        return "LingeringPotion"; //TODO
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        //TODO cancels spawning lingering potion projectile
        return true;
    }
}
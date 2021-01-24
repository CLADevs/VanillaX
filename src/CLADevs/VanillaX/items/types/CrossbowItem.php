<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\item\Tool;
use pocketmine\Player;

class CrossbowItem extends Tool{

    public function __construct(int $meta = 0){
        parent::__construct(self::CROSSBOW, $meta, "Crossbow");
    }

    public function getMaxDurability(): int{
        return 464;
    }

    public function onReleaseUsing(Player $player): bool{
        //TODO add support for firework and sounds
        $nbt = Entity::createBaseNBT(
            $player->add(0, $player->getEyeHeight(), 0),
            $player->getDirectionVector(),
            ($player->yaw > 180 ? 360 : 0) - $player->yaw,
            -$player->pitch
        );

        $diff = $player->getItemUseDuration();
        $p = $diff / 20;
        $baseForce = min((($p ** 2) + $p * 2) / 3, 1) * 4;

        $entity = Entity::createEntity("Arrow", $player->getLevelNonNull(), $nbt, $player, $baseForce >= 1);
        if($entity instanceof Arrow){
            $entity->setMotion($entity->getMotion()->multiply($baseForce));
            $entity->spawnToAll();
        }
        return true;
    }
}
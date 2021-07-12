<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class LingeringPotionItem extends ProjectileItem{

    public function __construct(int $meta = 0){
        parent::__construct(self::LINGERING_POTION, $meta, "Lingering Potion");
    }

    public function getThrowForce(): float{
        return 0.5;
    }

    public function getProjectileEntityType(): string{
        return "";
    }

    public function onClickAir(Player $player, Vector3 $directionVector) : bool{
//        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
//        $nbt->setShort("PotionId", $this->meta);
//
//        $projectile = new LingeringPotionEntity($player->getLevelNonNull(), $nbt, $player);
//        $projectile->setMotion($projectile->getMotion()->multiply($this->getThrowForce()));
//
//        $this->pop();
//
//        $projectileEv = new ProjectileLaunchEvent($projectile);
//        $projectileEv->call();
//        if($projectileEv->isCancelled()){
//            $projectile->flagForDespawn();
//        }else{
//            $projectile->spawnToAll();
//
//            $player->getLevelNonNull()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_THROW, 0, EntityBlockLegacyBlockLegacyIds::PLAYER);
//        }
//        $projectile->spawnToAll();
        return true;
    }
}
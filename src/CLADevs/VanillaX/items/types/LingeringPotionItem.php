<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class LingeringPotionItem extends ProjectileItem implements NonAutomaticCallItemTrait{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::LINGERING_POTION, 0), "Lingering Potion");
    }

    public function getThrowForce(): float{
        return 0.5;
    }

    public function getProjectileEntityType(): string{
        return "";
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
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
        return ItemUseResult::NONE();
    }

    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        // TODO: Implement createEntity() method.
    }
}
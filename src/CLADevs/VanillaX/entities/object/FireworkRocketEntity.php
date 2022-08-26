<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityCustomRegisterClosure;
use Closure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use pocketmine\world\World;

class FireworkRocketEntity extends Projectile implements EntityCustomRegisterClosure{

    const NETWORK_ID = EntityIds::FIREWORKS_ROCKET;

    public float $width = 0.25;
    public float $height = 0.25;

    private int $age;

    private bool $straight = true;

    public static function getRegisterClosure(): Closure{
        return function(World $world, CompoundTag $nbt): Entity{
            return new FireworkRocketEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        };
    }

    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
        parent::__construct($location, $shootingEntity, $nbt);
        $this->age = 20 + mt_rand(0, 1);
    }

    public function setStraight(bool $straight): void{
        $this->straight = $straight;
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);
        if($this->age > 0){
            $this->age--;
            if($this->straight){
                $this->motion->y += 0.05;
            }
        }else{
            if(!$this->isClosed() && !$this->isFlaggedForDespawn()){
                $pk = new ActorEventPacket();
                $pk->actorRuntimeId = $this->id;
                $pk->eventId = ActorEvent::FIREWORK_PARTICLES;
                $pk->eventData = 0;
                Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);
                $this->flagForDespawn();
            }
        }
        return $parent;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }

    public static function canRegister(): bool{
        return true;
    }
}
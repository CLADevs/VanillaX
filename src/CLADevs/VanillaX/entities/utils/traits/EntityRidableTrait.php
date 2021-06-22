<?php

namespace CLADevs\VanillaX\entities\utils\traits;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;

trait EntityRidableTrait{

    public ?Entity $rider = null;
    private bool $isSaddled = false;

    public function linkRider(Entity $rider, Vector3 $pos, bool $immediate = true, bool $causedByRider = true): void{
        $rider->setGenericFlag(Entity::DATA_FLAG_RIDING, true);
        $rider->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $pos);

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_RIDER, $immediate, $causedByRider);
        Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

        $this->rider = $rider;

        if($rider instanceof Player){
            /** @var VanillaEntity $var */
            $var = $this;
            VanillaX::getInstance()->getSessionManager()->get($rider)->setRidingEntity($var);
        }
    }

    public function unlinkRider(Entity $rider, $immediate = true, bool $causedByRider = true): void{
        $rider->setGenericFlag(Entity::DATA_FLAG_RIDING, false);
        $rider->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, new Vector3());

        /** @var VanillaEntity $this */
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_REMOVE, $immediate, $causedByRider);
        Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

        $this->rider = null;

        if($rider instanceof Player){
            VanillaX::getInstance()->getSessionManager()->get($rider)->setRidingEntity(null);
        }
    }

    public function readSaddle(): void{
        if($this->namedtag->hasTag("Saddled", ByteTag::class)){
            $this->isSaddled = boolval($this->namedtag->getByte("Saddled"));
            $this->setGenericFlag(Entity::DATA_FLAG_SADDLED, $this->isSaddled);
        }
    }

    public function writeSaddle(): void{
        $this->namedtag->setByte("Saddled", intval($this->isSaddled));
    }

    /**
     * @return Item[]
     */
    public function getSaddleDrops(): array{
        $drops = [];
        if($this->isSaddled){
            $drops[] = ItemFactory::get(ItemIds::SADDLE, 0, 1);
        }
        return $drops;
    }
}
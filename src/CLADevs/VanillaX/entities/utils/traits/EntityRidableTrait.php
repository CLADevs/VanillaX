<?php

namespace CLADevs\VanillaX\entities\utils\traits;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\Server;

trait EntityRidableTrait{

    public ?Entity $rider = null;
    private bool $isSaddled = false;

    public function linkRider(Entity $rider, Vector3 $pos, bool $immediate = true, bool $causedByRider = true): void{
        /** @var VanillaEntity $this */
        $rider->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);
        $rider->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, $pos);

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_RIDER, $immediate, $causedByRider);
        Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);

        $this->rider = $rider;

        if($rider instanceof Player){
            /** @var VanillaEntity $var */
            $var = $this;
            VanillaX::getInstance()->getSessionManager()->get($rider)->setRidingEntity($var);
        }
    }

    public function unlinkRider(Entity $rider, $immediate = true, bool $causedByRider = true): void{
        $rider->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
        $rider->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0, 0));

        /** @var VanillaEntity $this */
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_REMOVE, $immediate, $causedByRider);
        Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);

        $this->rider = null;

        if($rider instanceof Player){
            VanillaX::getInstance()->getSessionManager()->get($rider)->setRidingEntity(null);
        }
    }

    public function readSaddle(CompoundTag $nbt): void{
        $tag = $nbt->getTag("Saddled");

        if($tag instanceof StringTag){
            $this->isSaddled = boolval($tag->getValue());
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SADDLED, $this->isSaddled);
        }
    }

    public function writeSaddle(CompoundTag $nbt): void{
        $nbt->setByte("Saddled", intval($this->isSaddled));
    }

    /**
     * @return Item[]
     */
    public function getSaddleDrops(): array{
        $drops = [];
        if($this->isSaddled){
            $drops[] = ItemFactory::getInstance()->get(ItemIds::SADDLE, 0, 1);
        }
        return $drops;
    }
}
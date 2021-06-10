<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\EntityMouseHover;
use CLADevs\VanillaX\entities\utils\EntityRidable;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;

class StriderEntity extends VanillaEntity implements EntityInteractable, EntityMouseHover, EntityRidable{

    const TAG_SADDLED = "Saddled";

    const NETWORK_ID = self::STRIDER;

    public $width = 0.9;
    public $height = 1.7;

    public bool $isSaddled = false;
    public bool $hasRider = false;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        if($this->namedtag->hasTag(self::TAG_SADDLED, ByteTag::class)){
            $this->isSaddled = boolval($this->namedtag->getByte(self::TAG_SADDLED));
            $this->setGenericFlag(self::DATA_FLAG_SADDLED, $this->isSaddled);
        }
    }

    public function getName(): string{
        return "Strider";
    }

    public function saveNBT(): void{
        $this->namedtag->setByte(self::TAG_SADDLED, intval($this->isSaddled));
        parent::saveNBT();
    }

    public function onInteract(EntityInteractResult $result): void{
        $player = $result->getPlayer();
        $item = $result->getItem();

        if(!$this->isSaddled && $item->getId() === ItemIds::SADDLE){
            $this->isSaddled = true;
            $item->count--;
            $player->getInventory()->setItemInHand($item);
            $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_SADDLE);
            $this->setGenericFlag(self::DATA_FLAG_SADDLED, true);
        }elseif($this->isSaddled && !$this->hasRider){
            $this->onEnterRide($player);
        }
    }

    public function onMouseHover(Player $player): void{
        if(!$this->isSaddled && $player->getInventory()->getItemInHand()->getId() === ItemIds::SADDLE){
            $player->getDataPropertyManager()->setString(self::DATA_INTERACTIVE_TAG, "Saddle");
        }elseif($this->isSaddled && !$this->hasRider){
            $player->getDataPropertyManager()->setString(self::DATA_INTERACTIVE_TAG, "Ride");
        }
    }

    public function onEnterRide(Player $player): void{
        $player->setGenericFlag(self::DATA_FLAG_RIDING, true);
        $player->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, 2.8));

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_RIDER, true, true);
        Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

        $this->hasRider = true;
    }

    public function onLeftRide(Player $player): void{
        $player->setGenericFlag(self::DATA_FLAG_RIDING, false);
        $player->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3());

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_REMOVE, true, true);
        Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

        $this->hasRider = false;
    }
}
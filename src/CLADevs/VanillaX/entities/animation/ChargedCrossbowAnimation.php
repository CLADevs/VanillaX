<?php

namespace CLADevs\VanillaX\entities\animation;

use pocketmine\entity\animation\Animation;
use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

class ChargedCrossbowAnimation implements Animation{

    public function __construct(private Living $entity){}

    public function encode() : array{
        return [
            ActorEventPacket::create($this->entity->getId(), ActorEvent::CHARGED_CROSSBOW, 0)
        ];
    }
}
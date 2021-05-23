<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class ShulkerBoxInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 27, BlockIds::AIR, WindowTypes::CONTAINER);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $this->broadcastBlockEventPacket($who->getLevel(), true);
        $who->getLevel()->broadcastLevelSoundEvent($who, LevelSoundEventPacket::SOUND_SHULKER_OPEN);
    }

    public function onClose(Player $who): void{
        parent::onClose($who);
        $this->broadcastBlockEventPacket($who->getLevel(), false);
        $who->getLevel()->broadcastLevelSoundEvent($this->getHolder(), LevelSoundEventPacket::SOUND_SHULKER_CLOSE);
    }

    protected function broadcastBlockEventPacket(Level $level, bool $isOpen): void{
        $holder = $this->getHolder();

        $pk = new BlockEventPacket();
        $pk->x = (int) $holder->x;
        $pk->y = (int) $holder->y;
        $pk->z = (int) $holder->z;
        $pk->eventType = 1; //it's always 1 for a chest
        $pk->eventData = $isOpen ? 1 : 0;
        $level->broadcastPacketToViewers($holder, $pk);
    }
}
<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockIds;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class ShulkerBoxInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 27, BlockIds::AIR, WindowTypes::CONTAINER);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $this->broadcastBlockEventPacket($who->getLevel(), true);
    }

    public function onClose(Player $who): void{
        parent::onClose($who);
        $this->broadcastBlockEventPacket($who->getLevel(), false);
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

        $pk = new PlaySoundPacket();
        $pk->x = (int) $holder->x;
        $pk->y = (int) $holder->y;
        $pk->z = (int) $holder->z;
        $pk->soundName = $isOpen ? "random.shulkerboxopen" : "random.shulkerboxclosed";
        $pk->pitch = 1.0;
        $pk->volume = 1.0;
        $level->broadcastPacketToViewers($holder, $pk);
    }
}
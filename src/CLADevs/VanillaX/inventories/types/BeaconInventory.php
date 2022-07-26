<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\event\inventory\BeaconPaymentEvent;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\TemporaryInventory;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

class BeaconInventory extends FakeBlockInventory implements TemporaryInventory{

    const SLOT_PAYMENT = 0;

    public function __construct(Position $holder){
        parent::__construct($holder, 1, BlockLegacyIds::AIR, WindowTypes::BEACON);
    }

    public function onBeaconPayment(Player $player, int $primary, int $secondary): void{
        $ev = new BeaconPaymentEvent($player, $this, $this->getItem(self::SLOT_PAYMENT), $primary, $secondary);
        $ev->call();

        if($ev->isCancelled()){
            return;
        }
        $tile = $player->getWorld()->getTile($this->holder);

        if($tile instanceof BeaconTile){
            $tile->setPrimary($ev->getPrimary());
            $tile->setSecondary($ev->getSecondary());
            $tile->getPosition()->getWorld()->scheduleDelayedBlockUpdate($tile->getPosition(), 20);
        }
    }
}
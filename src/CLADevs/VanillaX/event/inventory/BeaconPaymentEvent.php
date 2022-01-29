<?php

namespace CLADevs\VanillaX\event\inventory;

use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class BeaconPaymentEvent extends Event implements Cancellable{
    use CancellableTrait;

    private Player $player;
    private BeaconInventory $inventory;
    private Item $item;

    public function __construct(Player $player, BeaconInventory $inventory, Item $item){
        $this->player = $player;
        $this->inventory = $inventory;
        $this->item = $item;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInventory(): BeaconInventory{
        return $this->inventory;
    }

    public function getItem(): Item{
        return $this->item;
    }
}

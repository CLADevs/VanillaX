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

    public function __construct(private Player $player, private BeaconInventory $inventory, private Item $payment, private int $primary, private int $secondary){
    }

    /**
     * @return Player
     * Whoever tried to put payment inside beacon
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * @return BeaconInventory
     * Whichever inventory player tried to put payment into
     */
    public function getInventory(): BeaconInventory{
        return $this->inventory;
    }

    /**
     * @return Item
     * Payment item they put in
     */
    public function getPayment(): Item{
        return $this->payment;
    }

    public function setPrimary(int $primary): void{
        $this->primary = $primary;
    }

    /**
     * @return int
     * Primary Effect for beacon
     */
    public function getPrimary(): int{
        return $this->primary;
    }
    
    public function setSecondary(int $secondary): void{
        $this->secondary = $secondary;
    }

    /**
     * @return int
     * Secondary Effect for beacon
     */
    public function getSecondary(): int{
        return $this->secondary;
    }
}

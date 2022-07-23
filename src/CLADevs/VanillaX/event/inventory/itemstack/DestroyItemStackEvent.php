<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestSlotInfo;
use pocketmine\player\Player;

class DestroyItemStackEvent extends ItemStackRequestEvent{

    public function __construct(protected Player $player, private ItemStackRequestSlotInfo $source){
        parent::__construct($this->player);
    }

    public function setSource(ItemStackRequestSlotInfo $source): void{
        $this->source = $source;
    }

    public function getSource(): ItemStackRequestSlotInfo{
        return $this->source;
    }
}

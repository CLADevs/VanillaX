<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\inventories\AnvilInventory;
use pocketmine\block\Anvil;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class AnvilBlock extends Anvil{

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
           $player->addWindow(new AnvilInventory($this), WindowTypes::ANVIL);
        }
        return true;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->getLevel()->broadcastLevelSoundEvent($this, LevelEventPacket::EVENT_SOUND_ANVIL_BREAK);
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}
<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\types\AnvilInventory;
use pocketmine\block\Anvil;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;
use pocketmine\world\sound\AnvilBreakSound;

class AnvilBlock extends Anvil{

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $this->getPos()->getWorld()->addSound($this->getPos(), new AnvilBreakSound());
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($player instanceof Player){
            $player->setCurrentWindow(new AnvilInventory($this->pos));
        }
        return true;
    }
}
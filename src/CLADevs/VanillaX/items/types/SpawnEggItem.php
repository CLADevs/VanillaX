<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\block\Block;
use pocketmine\entity\EntityBlockLegacyIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class SpawnEggItem extends SpawnEgg{

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        if($this->meta === EntityBlockLegacyBlockLegacyIds::VILLAGER){
            $this->meta = VanillaEntity::VILLAGER_V2;
        }
        return parent::onActivate($player, $blockReplace, $blockClicked, $face, $clickVector);
    }
}
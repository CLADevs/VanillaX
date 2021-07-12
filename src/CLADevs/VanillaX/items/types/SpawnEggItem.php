<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityBlockLegacyIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\World;

class SpawnEggItem extends SpawnEgg implements NonAutomaticCallItemTrait {

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
        //        if($this->meta === EntityIds::VILLAGER){
        //            $this->meta = VanillaEntity::VILLAGER_V2;
        //        }
        return parent::onInteractBlock($player, $blockReplace, $blockClicked, $face, $clickVector);
    }

    protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity
    {
        // TODO: Implement createEntity() method.
    }
}
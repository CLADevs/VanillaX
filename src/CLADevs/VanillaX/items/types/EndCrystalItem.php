<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\EnderCrystalEntity;
use pocketmine\block\Bedrock;
use pocketmine\block\Block;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EndCrystalItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::END_CRYSTAL, 0), "End Crystal");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
        if($blockClicked instanceof Bedrock && $face === Facing::UP){
            $entity = new EnderCrystalEntity(Location::fromObject(clone $blockReplace->getPosition()->add(0.5, 0, 0.5), $blockReplace->getPosition()->getWorld()));
            $entity->spawnToAll();
            if($player->isSurvival() || $player->isAdventure()) $this->pop();
            return ItemUseResult::SUCCESS();
        }
        return ItemUseResult::FAIL();
    }
}
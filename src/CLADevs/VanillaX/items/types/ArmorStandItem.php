<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\ArmorStandEntity;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class ArmorStandItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::ARMOR_STAND, 0), "Armor Stand");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
        $location = $player->getLocation();
        $location->x += 0.5;
        $location->z += 0.5;
        $entity = new ArmorStandEntity($location);
        $entity->lookAt($player->getPosition());
        Session::playSound($player, "mob.armor_stand.place");
        if($player->isSurvival() || $player->isAdventure()) $this->pop();
        return ItemUseResult::SUCCESS();
    }
}
<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\ArmorStandEntity;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class ArmorStandItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::ARMOR_STAND, $meta, "Armor Stand");
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        $entity = new ArmorStandEntity($player->getLevel(), ArmorStandEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null));
        $entity->lookAt($player);
        $entity->spawnToAll();
        Session::playSound($player, "mob.armor_stand.place");
        if($player->isSurvival() || $player->isAdventure()) $this->pop();
        return true;
    }
}
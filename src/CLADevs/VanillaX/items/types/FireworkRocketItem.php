<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\FireworkRocketEntity;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FireworkRocketItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIREWORKS, $meta, "Firework Rocket");
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
       $this->onItemUse($player, $blockReplace->add(0, 1));
       return true;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        $this->onItemUse($player);
        return true;
    }

    public function onItemUse(Player $player, Vector3 $pos = null): void{
        $pos = $pos ?? $player;
        $entity = new FireworkRocketEntity($player->getLevel(), FireworkRocketEntity::createBaseNBT($pos->subtract(0, 1)), $player);
        $entity->spawnToAll();
        Session::playSound($player, "firework.launch");
        if($player->isSurvival() || $player->isAdventure()) $this->pop();
    }
}
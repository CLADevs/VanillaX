<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\FireworkRocketEntity;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class FireworkRocketItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIREWORKS, $meta, "Firework Rocket");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        $this->checkElytra($player);
        return parent::onClickAir($player, $directionVector);
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        if(!$this->checkElytra($player)){
            $entity = new FireworkRocketEntity($player->getLevel(), FireworkRocketEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5)), $player);
            if($this->getNamedTag()->hasTag("Fireworks", CompoundTag::class)){
                $entity->getDataPropertyManager()->setCompoundTag(16, $this->getNamedTag());
            }
            $entity->spawnToAll();
        }
        Session::playSound($player, "firework.launch");
        if($player->isSurvival() || $player->isAdventure()) $this->pop();
       return true;
    }

    public function checkElytra(Player $player): bool{
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($player->getArmorInventory()->getChestplate() instanceof ElytraItem && $session->isGliding()){
            $player->setMotion($player->getDirectionVector()->multiply(1.6));
            $this->pop();
            return true;
        }
        return false;
    }
}
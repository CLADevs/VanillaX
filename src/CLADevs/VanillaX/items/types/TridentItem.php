<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\projectile\TridentEntity;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\BlockIds;

use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;

class TridentItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::TRIDENT, $meta, "Trident");
    }

    public function onReleaseUsing(Player $player): bool{
        if($this->hasEnchantment(Enchantment::RIPTIDE)){
            if($player->getLevel()->getBlock($player->add(0, 1)) instanceof Water){
                $this->spawnTride($player);
            }
        }else{
            $this->spawnTride($player);
        }
        return true;
    }

    public function spawnTride(Player $player): void{
        $nbt = Entity::createBaseNBT(
            $player->add(0, $player->getEyeHeight(), 0),
            $player->getDirectionVector(),
            ($player->yaw > 180 ? 360 : 0) - $player->yaw,
            -$player->pitch
        );

        $diff = $player->getItemUseDuration();
        $p = $diff / 20;
        $baseForce = min((($p ** 2) + $p * 2) / 3, 1);
        $entity = new TridentEntity($player->getLevel(), $nbt, clone $this, $player);
        $entity->setMotion($entity->getMotion()->multiply($baseForce));
        $entity->spawnToAll();
        Session::playSound($player, "item.trident.throw");
        $this->pop();
        $player->getInventory()->setItemInHand($this);
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
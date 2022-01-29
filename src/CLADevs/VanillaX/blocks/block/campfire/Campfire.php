<?php

namespace CLADevs\VanillaX\blocks\block\campfire;

use CLADevs\VanillaX\blocks\tile\campfire\CampfireTile;
use CLADevs\VanillaX\blocks\utils\FacingPlayerTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use Exception;
use pocketmine\block\Opaque;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Campfire extends Opaque implements NonAutomaticCallItemTrait{
    use FacingPlayerTrait;

    /**
     * @param Item $item
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     * @return bool
     * @throws Exception
     */
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $tile = $this->position->getWorld()->getTile($this->position);

            if($tile instanceof CampfireTile && $tile->addItem(clone $item->setCount(1))){
                $item->pop();
                $this->position->world->setBlock($this->position, $this);
                return true;
            }
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function onScheduledUpdate(): void{
        $tile = $this->position->getWorld()->getTile($this->position);

        if($tile instanceof CampfireTile && !$tile->closed){
            foreach($tile->getContents() as $slot => $item){
                $tile->increaseSlotTime($slot);

                if($tile->getItemTime($slot) >= CampfireTile::MAX_COOK_TIME){
                    $tile->setItem(ItemFactory::air(), $slot);
                    $tile->setSlotTime($slot, 0);
                    $this->position->world->setBlock($this->position, $this);

                    $result = ItemFactory::getInstance()->get($tile->getRecipes()[$item->getId()] ?? $item->getId());
                    $this->position->getWorld()->dropItem($this->position->add(0, 1, 0), $result);
                }
            }
        }
        $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 20);
    }

    public function onEntityInside(Entity $entity): bool{
        if($entity instanceof Player && $entity->isCreative()){
            return false;
        }
        $entity->setOnFire(8);
        return true;
    }

    public function hasEntityCollision(): bool{
        return true;
    }

    public function getStateBitmask(): int{
        return 0b111;
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\entities\utils\traits\EntityRidableTrait;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\HeldItemChangeTrait;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class StriderEntity extends VanillaEntity implements EntityInteractable, InteractButtonItemTrait, EntityRidable, HeldItemChangeTrait{
    use EntityRidableTrait;

    const BUTTON_SADDLE = "Saddle";
    const BUTTON_RIDE = "Ride";
    const BUTTON_BOOST = "Boost";

    const NETWORK_ID = self::STRIDER;

    public $width = 0.9;
    public $height = 1.7;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->readSaddle();
    }

    public function getName(): string{
        return "Strider";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 2, 5);
        return array_merge([$string], $this->getSaddleDrops());
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function saveNBT(): void{
        $this->writeSaddle();
        parent::saveNBT();
    }

    public function onInteract(EntityInteractResult $result): void{
        $player = $result->getPlayer();
        $this->onButtonPressed(new InteractButtonResult($player, $result->getItem(), $player->getDataPropertyManager()->getString(self::DATA_INTERACTIVE_TAG)));
    }

    public function onMouseHover(Player $player): void{
        $this->recalculateButton($player, $player->getInventory()->getItemInHand());
    }

    public function onSlotChange(Player $player, Item $old, Item $new): void{
        if($old->getId() !== $new->getId()){
            $this->recalculateButton($player, $new);
        }
    }

    public function onEnterRide(Player $player): void{
        $this->linkRider($player, new Vector3(0, 2.8, -0.2));
        $this->recalculateButton($player, $player->getInventory()->getItemInHand());
    }

    public function onLeftRide(Player $player): void{
        $this->unlinkRider($player);
        $player->getDataPropertyManager()->setString(self::DATA_INTERACTIVE_TAG, "");
    }

    public function onButtonPressed(InteractButtonResult $result): void{
        if(($name = $result->getButton()) === null){
            return;
        }
        $player = $result->getPlayer();
        $item = $result->getItem();

        switch($name){
            case self::BUTTON_SADDLE:
                if(!$this->isSaddled && $item->getId() === ItemIds::SADDLE){
                    $this->isSaddled = true;
                    if(!$player->isCreative()){
                        $item->count--;
                        $player->getInventory()->setItemInHand($item);
                    }
                    $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_SADDLE);
                    $this->setGenericFlag(self::DATA_FLAG_SADDLED, true);
                }
                break;
            case self::BUTTON_RIDE:
                if($this->isSaddled && $this->rider === null){
                    $this->onEnterRide($player);
                }
                break;
            case self::BUTTON_BOOST:
                if($this->isSaddled && $this->rider === $player && $item->getId() === ItemIds::CARROT_ON_A_STICK){
                    //TODO boost
                }
                break;
        }
    }

    public function recalculateButton(Player $player, Item $item): void{
        $tag = "";

        if(!$this->isSaddled && $item->getId() === ItemIds::SADDLE){
            $tag = self::BUTTON_SADDLE;
        }elseif($this->isSaddled && $this->rider === null){
            $tag = self::BUTTON_RIDE;
        }elseif($this->isSaddled && $this->rider === $player && $item->getId() === ItemIds::CARROT_ON_A_STICK){
            $tag = self::BUTTON_BOOST;
        }
        $player->getDataPropertyManager()->setString(self::DATA_INTERACTIVE_TAG, $tag);
    }
}
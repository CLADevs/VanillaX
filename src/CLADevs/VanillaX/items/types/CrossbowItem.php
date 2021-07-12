<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\FireworkRocketEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow as ArrowProjectile;
use pocketmine\item\Arrow;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;

class CrossbowItem extends Tool{

    const TAG_CHARGED_ITEM = "chargedItem";

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::CROSSBOW, 0), "Crossbow");
    }

    public function getMaxDurability(): int{
        return 464;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
        //        if($this->isCharged()){
        //            $this->fireProjectile($player);
        //            return false;
        //        }
        //        $this->chargeBow($player);
        //        return false;
        return parent::onClickAir($player, $directionVector);
    }

    private function chargeBow(Player $player): void{
        $duration = $player->getItemUseDuration();

        if($duration >= 24){
            $offhandItem = $player->getOffHandInventory()->getItem(0);
            $itemId = $offhandItem->getId() === ItemIds::FIREWORKS ? ItemIds::FIREWORKS : ItemIds::ARROW;
            $this->setCharged($player, true, $itemId);
            $player->getInventory()->setItemInHand($this);
            $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_CROSSBOW_LOADING_MIDDLE);
        }elseif($duration < 12){
            $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_CROSSBOW_LOADING_START);
        }else{
            $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_CROSSBOW_LOADING_MIDDLE);
        }
    }

    private function fireProjectile(Player $player): void{
        $projectile = $this->getChargedProjectile();
        $nbt = Entity::createBaseNBT(
            $player->add(0, $player->getEyeHeight(), 0),
            $player->getDirectionVector()->multiply(2),
            ($player->yaw > 180 ? 360 : 0) - $player->yaw,
            -$player->pitch
        );
        $entity = null;

        if($projectile instanceof Arrow){
            $entity = Entity::createEntity("Arrow", $player->getLevelNonNull(), $nbt, $player, true);
        }elseif($projectile instanceof FireworkRocketItem){
            $entity = new FireworkRocketEntity($player->getLevelNonNull(), $nbt);
            $entity->setStraight(false);
        }
        if($entity instanceof ArrowProjectile || $entity instanceof FireworkRocketEntity){
            $entity->spawnToAll();
            $this->setCharged($player, false);
            $player->getInventory()->setItemInHand($this);
            $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_CROSSBOW_SHOOT);
        }
    }

    public function getChargedProjectile(): ?Item{
        if($this->isCharged()){
            return Item::nbtDeserialize($this->getNamedTag()->getCompoundTag(self::TAG_CHARGED_ITEM));
        }
        return null;
    }

    public function isCharged(): bool{
        return $this->getNamedTag()->hasTag(self::TAG_CHARGED_ITEM);
    }

    public function setCharged(Player $player, bool $value, int $itemId = ItemIds::ARROW){
        if(!$value){
            if($this->getNamedTag()->hasTag(self::TAG_CHARGED_ITEM)){
                $this->getNamedTag()->removeTag(self::TAG_CHARGED_ITEM);
            }
            return;
        }
        $item = ItemFactory::getInstance()->get($itemId);
        $nbt = $item->nbtSerialize(-1, self::TAG_CHARGED_ITEM);
        $nbt->setString(self::TAG_DISPLAY_NAME, "minecraft:" . strtolower(str_replace(" ", "_", $item->getVanillaName())));
        $this->setNamedTagEntry($nbt);

        if($itemId === ItemIds::ARROW){
            if($player->isSurvival() and $player->getInventory()->contains(ItemFactory::getInstance()->get(Item::ARROW, 0, 1))){
                $player->getInventory()->removeItem(ItemFactory::getInstance()->get(Item::ARROW, 0, 1));
            }
        }elseif($itemId === ItemIds::FIREWORKS){
            $offhand = VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory();

            if($player->isSurvival() and $offhand->contains(ItemFactory::getInstance()->get(Item::FIREWORKS, 0, 1))){
                $offhand->removeItem(ItemFactory::getInstance()->get(Item::FIREWORKS, 0, 1));
            }
        }
    }
}
<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow as ArrowProjectile;
use pocketmine\item\Arrow;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class CrossbowItem extends Tool{

    const TAG_CHARGED_ITEM = "chargedItem";

    private bool $isLoadingStart = false;
    private bool $isLoadingMiddle = false;

    public function __construct(int $meta = 0){
        parent::__construct(self::CROSSBOW, $meta, "Crossbow");
    }

    public function getMaxDurability(): int{
        return 464;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        if($this->isCharged()){
            $this->fireProjectile($player);
            return false;
        }
        $this->chargeBow($player);
        return false;
    }

    private function chargeBow(Player $player): void{
        $duration = $player->getItemUseDuration();

        if($duration >= 24){
            $this->setCharged($player, true);
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

        if($projectile instanceof Arrow){
            $nbt = Entity::createBaseNBT(
                $player->add(0, $player->getEyeHeight(), 0),
                $player->getDirectionVector()->multiply(2),
                ($player->yaw > 180 ? 360 : 0) - $player->yaw,
                -$player->pitch
            );
            $entity = Entity::createEntity("Arrow", $player->getLevelNonNull(), $nbt, $player, true);

            if($entity instanceof ArrowProjectile){
                $entity->spawnToAll();
                $this->setCharged($player, false);
                $player->getInventory()->setItemInHand($this);
                $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_CROSSBOW_SHOOT);
            }
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
        $item = ItemFactory::get($itemId);
        $nbt = $item->nbtSerialize(-1, self::TAG_CHARGED_ITEM);
        $nbt->setString(self::TAG_DISPLAY_NAME, "minecraft:" . strtolower(str_replace(" ", "_", $item->getVanillaName())));
        $this->setNamedTagEntry($nbt);

        if($itemId === ItemIds::ARROW){
            if($player->isSurvival() and $player->getInventory()->contains(ItemFactory::get(Item::ARROW, 0, 1))){
                $player->getInventory()->removeItem(ItemFactory::get(Item::ARROW, 0, 1));
            }
        }
    }
}
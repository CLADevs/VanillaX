<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\animation\ChargedCrossbowAnimation;
use CLADevs\VanillaX\entities\object\FireworkRocketEntity;
use CLADevs\VanillaX\entities\projectile\ArrowEntity;
use CLADevs\VanillaX\event\entity\EntityCrossbowLaunchEvent;
use CLADevs\VanillaX\event\entity\EntityCrossbowLoadedEvent;
use CLADevs\VanillaX\session\Session;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\entity\Location;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Releasable;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class CrossbowItem extends Tool implements Releasable{

    const TAG_CHARGED_ITEM = "chargedItem";

    const LOAD_TIME = 25;

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::CROSSBOW, 0), "Crossbow");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
        $diff = $player->getItemUseDuration();
        $expectedDiff = $this->getQuickChargeLevel() * 5;
        $p = ($expectedDiff + 10) / 25;
        $baseForce = (min((($p ** 2) + $p * 2) / 3, 1)) * 3;

        if($this->hasChargedItem()){
            return $this->handleCharged($player, $baseForce);
        }
        $arrow = VanillaItems::ARROW();
        $fireworks = ItemFactory::getInstance()->get(ItemIds::FIREWORKS);
        [$inventory, $item] = match(true){
            $player->getOffHandInventory()->contains($fireworks) => [$player->getOffHandInventory(), $fireworks],
            $player->getOffHandInventory()->contains($arrow) => [$player->getOffHandInventory(), $arrow],
            $player->getInventory()->contains($arrow) => [$player->getInventory(), $arrow],
            default => [null, null]
        };

        if($player->hasFiniteResources() && $inventory === null){
            return ItemUseResult::FAIL();
        }else{
            if($item === null){
                $inventory = null;
                $item = $arrow;
            }
        }
        if($diff >= (self::LOAD_TIME - $expectedDiff)){
            return $this->handleLoaded($player, $inventory, $item);
        }
        if(!$this->hasChargedItem() && !$player->isUsingItem()){
            Session::playSound($player, $this->hasPiercing() ? "crossbow.quick_charge.start" : "crossbow.loading.start");
            return ItemUseResult::SUCCESS();
        }
        return ItemUseResult::FAIL();
    }

    private function handleCharged(Player $player, float $baseForce): ItemUseResult{
        $location = $player->getLocation();
        $item = $this->getChargedItem();
        $ev = new EntityCrossbowLaunchEvent($player, $this, $item, $baseForce);
        $ev->call();

        if($ev->isCancelled()){
            return ItemUseResult::FAIL();
        }
        $baseForce = $ev->getForce();

        for($i = 0; $i < ($this->hasMultiShot() ? 3 : 1); $i++){
            $rad = $i === 1 ? -1 : ($i === 2 ? 1 : 0);
            $loc = Location::fromObject(
                $player->getEyePos()->add($rad, 0, $rad),
                $player->getWorld(),
                ($location->yaw > 180 ? 360 : 0) - $location->yaw,
                -$location->pitch
            );

            if($item instanceof FireworkRocketItem){
                $entity = new FireworkRocketEntity($loc, $player);
                $entity->setStraight(false);
            }else{
                $entity = new ArrowEntity($loc, $player, true);
                $entity->setPierce($this->getPiercingLevel());
            }
            $entity->setMotion($player->getDirectionVector()->multiply($baseForce));
            $entity->spawnToAll();
        }
        $this->removeChargedItem();
        $player->getInventory()->setItemInHand($this);
        Session::playSound($player, "crossbow.shoot");
        return ItemUseResult::FAIL(); //Making this success executes setUsingItem which we dont want
    }

    private function handleLoaded(Player $player, ?Inventory $inventory, Item $projectile): ItemUseResult{
        $ev = new EntityCrossbowLoadedEvent($player, $this, $projectile);
        $ev->call();
        if($ev->isCancelled()){
            return ItemUseResult::FAIL();
        }

        $inventory?->removeItem($projectile);
        $this->setChargedItem($projectile);
        $player->setUsingItem(false);
        $player->broadcastAnimation(new ChargedCrossbowAnimation($player));
        $player->getInventory()->setItemInHand($this);
        Session::playSound($player, $this->hasPiercing() ? "crossbow.quick_charge.end" : "crossbow.loading.end");
        return ItemUseResult::SUCCESS();
    }

    public function getQuickChargeLevel(): int{
        return $this->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::QUICK_CHARGE));
    }

    public function hasQuickCharge(): bool{
        return $this->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::QUICK_CHARGE));
    }

    public function getMultiShotLevel(): int{
        return $this->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::MULTISHOT));
    }

    public function hasMultiShot(): bool{
        return $this->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::MULTISHOT));
    }

    public function getPiercingLevel(): int{
        return $this->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::PIERCING));
    }

    public function hasPiercing(): bool{
        return $this->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::PIERCING));
    }

    public function hasChargedItem(): bool{
        return $this->getNamedTag()->getTag(self::TAG_CHARGED_ITEM) !== null;
    }

    public function getChargedItem(): Item{
        return Item::nbtDeserialize($this->getNamedTag()->getCompoundTag(self::TAG_CHARGED_ITEM));
    }

    public function setChargedItem(Item $item): void{
        $this->getNamedTag()->setTag(self::TAG_CHARGED_ITEM, $item->nbtSerialize());
    }

    public function removeChargedItem(): void{
        $this->getNamedTag()->removeTag(self::TAG_CHARGED_ITEM);
    }

    public function getMaxDurability(): int{
        return 464;
    }

    public function canStartUsingItem(Player $player): bool{
        return (!$player->hasFiniteResources() || $player->getOffHandInventory()->contains($arrow = VanillaItems::ARROW()) || $player->getInventory()->contains($arrow) || $player->getOffHandInventory()->contains(ItemFactory::getInstance()->get(ItemIds::FIREWORKS))) && !$this->hasChargedItem();
    }
}
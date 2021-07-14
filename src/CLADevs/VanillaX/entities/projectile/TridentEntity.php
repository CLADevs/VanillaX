<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\TakeItemActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class TridentEntity extends Projectile{

    public float $width = 0.25;
    public float $height = 0.35;

    /** @var float */
    protected $gravity = 0.05;
    /** @var float */
    protected $drag = 0.01;

    const NETWORK_ID = EntityIds::THROWN_TRIDENT;

    private Item $parent;
    private bool $hasLoyalty = false;
    private bool $canReturn = false;
    private bool $isReturnSoundPlayed = false;
    private bool $hitSoundPlayed = false;

    private int $returnTick = 10;
    private ?int $forceReturnTick = null;

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);

        if($this->canReturn && $this->hasLoyalty){
            if($this->returnTick > 0) $this->returnTick--;
            if($this->returnTick <= 0){
                if($this->forceReturnTick !== null && $this->forceReturnTick > 0) $this->forceReturnTick--;
                $this->tryChangeMovement();
                $x = $this->getOwningEntity()->getPosition()->x - $this->getPosition()->x;
                $z = $this->getOwningEntity()->getPosition()->z - $this->getPosition()->z;
                $xz = sqrt($x * $x + $z * $z);
                $x /= $xz;
                $z /= $xz;
                $y = 0;

                //TODO showe trident rope
//                if(!$this->getGenericFlag(self::DATA_FLAG_SHOW_TRIDENT_ROPE)){
//                    $this->setGenericFlag(self::DATA_FLAG_SHOW_TRIDENT_ROPE, true);
//                }
                //TODO No Clip
                if($this->forceReturnTick === null){
                  //  $this->forceReturnTick = min(mt_rand(20 * 4, 20 * 6), $x + $z * 4);
                    $this->forceReturnTick = mt_rand(20 * 4, 20 * 6);
                }
                if($this->getPosition()->y < $this->getOwningEntity()->getPosition()->y){
                    $diff = $this->getOwningEntity()->getPosition()->y - $this->getPosition()->y;
                    $y = ($diff / 10);
                }elseif($this->getPosition()->y > ($this->getOwningEntity()->getPosition()->y + 4)){
                    $y = -0.2;
                }
                $this->setMotion(new Vector3($x, $y, $z));
                $this->move($x, $y, $z);
                $this->updateMovement();

                $owner = $this->getOwningEntity();
                if(!$this->isReturnSoundPlayed){
                    if($owner instanceof Player){
                        Session::playSound($owner, "item.trident.return");
                        $this->isReturnSoundPlayed = true;
                    }
                }
                if($this->forceReturnTick !== null && $this->forceReturnTick <= 0 && !$this->isFlaggedForDespawn() && $owner instanceof Player){
                    $this->onCollideWithPlayer($owner);
                }
            }
        }
        return $parent;
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
        if(!$this->hitSoundPlayed){
            $owner = $this->getOwningEntity();

            if($owner instanceof Player){
                Session::playSound($owner, "item.trident.hit_ground");
                $this->hitSoundPlayed = true;
            }
        }
        $this->canReturn = true;
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        $damage = $this->getResultDamage();

        if($damage >= 0){
            if($this->getOwningEntity() === null){
                $ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }else{
                $ev = new EntityDamageByChildEntityEvent($this->getOwningEntity(), $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }

            $entityHit->attack($ev);

            if($this->isOnFire()){
                $ev = new EntityCombustByEntityEvent($this, $entityHit, 5);
                $ev->call();
                if(!$ev->isCancelled()){
                    $entityHit->setOnFire($ev->getDuration());
                }
            }
        }
        if(!$this->hitSoundPlayed){
            $owner = $this->getOwningEntity();

            if($owner instanceof Player){
                Session::playSound($owner, "item.trident.hit");
                $this->hitSoundPlayed = true;
            }
        }
        $this->canReturn = true;
        //$this->flagForDespawn();
    }

    public function onCollideWithPlayer(Player $player): void{
        if(!$this->canReturn) return;
        if($this->hasLoyalty){
            /** @var Player|null $owner */
            $owner = $this->getOwningEntity();
            if(!$owner instanceof Player || $player->getName() !== $owner->getName()){
                return;
            }
        }
        $pk = new TakeItemActorPacket();
        $pk->eid = $player->getId();
        $pk->target = $this->getId();
        $this->server->broadcastPackets($this->getViewers(), [$pk]);

        $player->getInventory()->addItem($this->parent);
        $this->flagForDespawn();
    }

    public function flagForDespawn(): void{
        $owner = $this->getOwningEntity();
        if($owner instanceof Player){
            $session = VanillaX::getInstance()->getSessionManager()->get($owner);
            $session->removeTrident($this);
        }
        parent::flagForDespawn();
    }

    public function setOwningEntity(?Entity $owner): void{
        parent::setOwningEntity($owner);
        if($owner !== null){
            if($owner instanceof Player){
                $session = VanillaX::getInstance()->getSessionManager()->get($owner);
                $session->addTrident($this);
            }
            $this->getNetworkProperties()->setLong(EntityMetadataProperties::OWNER_EID, $owner->getId());
        }
    }

    public function setParent(Item $parent): void{
        $this->parent = $parent;
        if($parent->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOYALTY))){
            $this->hasLoyalty = true;
        }
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
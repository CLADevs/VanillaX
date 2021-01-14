<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\TakeItemActorPacket;
use pocketmine\Player;

class TridentEntity extends Projectile{

    public $width = 0.25;
    public $height = 0.35;

    protected $gravity = 0.05;
    protected $drag = 0.01;

    const NETWORK_ID = self::THROWN_TRIDENT;

    private Item $parent;
    private bool $hasLoyalty = false;
    private bool $canReturn = false;
    private bool $isReturnSoundPlayed = false;
    private bool $hitSoundPlayed = false;

    private int $returnTick = 10;
    private ?int $forceReturnTick = null;

    public function __construct(Level $level, CompoundTag $nbt, Item $item, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
        $this->parent = $item;
        if($item->hasEnchantment(Enchantment::LOYALTY)){
            $this->hasLoyalty = true;
        }
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);

        if($this->canReturn && $this->hasLoyalty){
            if($this->returnTick > 0) $this->returnTick--;
            if($this->returnTick <= 0){
                if($this->forceReturnTick !== null && $this->forceReturnTick > 0) $this->forceReturnTick--;
                $this->tryChangeMovement();
                $x = $this->getOwningEntity()->x - $this->x;
                $z = $this->getOwningEntity()->z - $this->z;
                $xz = sqrt($x * $x + $z * $z);
                $x /= $xz;
                $z /= $xz;
                $y = 0;

                if($this->forceReturnTick === null){
                  //  $this->forceReturnTick = min(mt_rand(20 * 4, 20 * 6), $x + $z * 4);
                    $this->forceReturnTick = mt_rand(20 * 4, 20 * 6);
                }
                if($this->y < $this->getOwningEntity()->getY()){
                    $diff = $this->getOwningEntity()->getY() - $this->y;
                    $y = ($diff / 10);
                }elseif($this->y > ($this->getOwningEntity()->getY() + 4)){
                    $y = -0.2;
                }
                $this->setMotion(new Vector3($x, $y, $z));
                $this->move($x, $y, $z);
                //TODO make it go thr wall and add particle thingy

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
        $this->server->broadcastPacket($this->getViewers(), $pk);

        $player->getInventory()->addItem($this->parent);
        $this->flagForDespawn();
    }
}
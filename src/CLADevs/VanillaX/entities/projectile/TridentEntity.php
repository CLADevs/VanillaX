<?php

namespace CLADevs\VanillaX\entities\projectile;

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

    public function __construct(Level $level, CompoundTag $nbt, Item $item, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
        $this->parent = $item;
        if($item->hasEnchantment(Enchantment::LOYALTY)){
            $this->hasLoyalty = true;
        }
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);

//        if($this->hasLoyalty && $this->canReturn){
//            $x = abs($this->x - $this->getOwningEntity()->x);
//            $y = abs($this->y - $this->getOwningEntity()->y);
//            $z = abs($this->z - $this->getOwningEntity()->z);
//            var_dump("$x, $y, $z");
//        }
        return $parent;
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
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
        //$this->flagForDespawn();
        $this->canReturn = true;
    }


    public function onCollideWithPlayer(Player $player): void{
        if($this->canReturn === null) return;
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
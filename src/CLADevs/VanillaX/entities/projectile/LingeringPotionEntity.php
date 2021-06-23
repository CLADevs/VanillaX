<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\entities\object\AreaEffectCloudEntity;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Potion;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\utils\Color;

class LingeringPotionEntity extends Throwable{

    const NETWORK_ID = self::LINGERING_POTION;

    protected $gravity = 0.05;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER, true);
        $this->propertyManager->setShort(self::DATA_POTION_AUX_VALUE, $this->namedtag->getShort("PotionId", 0));
    }

    public function saveNBT(): void{
        parent::saveNBT();
        $this->namedtag->setShort("PotionId", $this->getPotionId());
    }

    protected function onHit(ProjectileHitEvent $event) : void{
        $effects = $this->getPotionEffects();
        $hasEffects = true;

        if(count($effects) === 0){
            $colors = [
                new Color(0x38, 0x5d, 0xc6) //Default colour for splash water bottle and similar with no effects.
            ];
            $hasEffects = false;
        }else{
            $colors = [];
            foreach($effects as $effect){
                $level = $effect->getEffectLevel();
                for($j = 0; $j < $level; ++$j){
                    $colors[] = $effect->getColor();
                }
            }
        }

        $this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_PARTICLE_SPLASH, Color::mix(...$colors)->toARGB());
        $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);

        if($hasEffects){
            $effectId = -1;
            foreach($effects as $effect){
                $effectId = $effect->getId();
            }
            if($effectId !== -1){
                $nbt = AreaEffectCloudEntity::createBaseNBT($this);
                $nbt->setShort("PotionId", $effectId);
                $areaCloudEffect = new AreaEffectCloudEntity($this->getLevel(), $nbt);
                $areaCloudEffect->spawnToAll();
            }
        }elseif($event instanceof ProjectileHitBlockEvent and $this->getPotionId() === Potion::WATER){
            $blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

            if($blockIn->getId() === Block::FIRE){
                $this->level->setBlock($blockIn, BlockFactory::get(Block::AIR));
            }
            foreach($blockIn->getHorizontalSides() as $horizontalSide){
                if($horizontalSide->getId() === Block::FIRE){
                    $this->level->setBlock($horizontalSide, BlockFactory::get(Block::AIR));
                }
            }
        }
    }

    public function getResultDamage(): int{
        return -1; //no damage
    }

    /**
     * @return EffectInstance[]
     */
    public function getPotionEffects(): array{
        return Potion::getPotionEffectsById($this->getPotionId());
    }

    public function getPotionId(): int{
        return $this->propertyManager->getShort(self::DATA_POTION_AUX_VALUE) ?? 0;
    }
}
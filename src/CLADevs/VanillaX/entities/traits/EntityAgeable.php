<?php

namespace CLADevs\VanillaX\entities\traits;

use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;

class EntityAgeable{

    /** @var Item[] */
    private array $growthItems = [];

    private LivingEntity $parent;

    private bool $canBeBredByPlayer = true;
    private bool $canBeLeashed = true;
    private bool $isGrownup = false;
    private int $duration = 0;
    private int $maxDuration = 1200;
    private int $foodPoint = 120;

    /** [width, height] */
    public array $adultScale = [];
    public array $babyScale = [];

    public function __construct(LivingEntity $parent, array $babyScale = [], array $adultScale = []){
        $this->parent = $parent;
        $this->babyScale = $babyScale;
        $this->adultScale = $adultScale;
        $this->recalculateScale();
    }

    public function tick(): void{
        if($this->isGrownup){
            return;
        }elseif(!$this->isBaby()){
            $this->isGrownup = true;
            return;
        }
        if($this->duration < $this->maxDuration){
            $this->duration++;
        }else{
            $this->isGrownup = true;
            $this->setBaby(false);
        }
    }

    public function initializeScale(bool $adult = false): void{
        if($adult){
            $this->parent->width = $this->adultScale[0];
            $this->parent->height = $this->adultScale[1];
        }else{
            $this->parent->width = $this->babyScale[0];
            $this->parent->height = $this->babyScale[1];
        }
        $this->parent->recalculateBoundingBox();
    }

    public function onFed(Player $player): void{
        if($this->isGrownup){
            $this->duration -= $this->foodPoint;
        }
    }

    public function recalculateScale(): void{
        $this->initializeScale(!$this->isBaby());
    }

    public function getMaxDuration(): int{
        return $this->maxDuration;
    }

    /**
     * @return Item[]
     */
    public function getGrowthItems(): array{
        return $this->growthItems;
    }

    /**
     * @param Item[]|int[] $growthItems
     */
    public function setGrowthItems(array $growthItems): void{
        foreach($growthItems as $key => $item){
            if(is_int($item)){
                $growthItems[$key] = ItemFactory::get($item);
            }
        }
        $this->growthItems = $growthItems;
    }

    public function isBaby(): bool{
        return $this->parent->getGenericFlag(Entity::DATA_FLAG_BABY);
    }

    public function setBaby(bool $value): void{
        $this->parent->setGenericFlag(Entity::DATA_FLAG_BABY, $value);
        $this->recalculateScale();
    }

    public function canBeBredByPlayer(): bool{
        return $this->canBeBredByPlayer;
    }

    public function setCanBeBredByPlayer(bool $canBeBredByPlayer): void{
        $this->canBeBredByPlayer = $canBeBredByPlayer;
    }

    public function canBeLeashed(): bool{
        return $this->canBeLeashed;
    }

    public function setCanBeLeashed(bool $canBeLeashed): void{
        $this->canBeLeashed = $canBeLeashed;
    }
}
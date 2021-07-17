<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LlamaEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::LLAMA;

    public $width = 0.9;
    public $height = 1.87;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Llama";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $leather = ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $leather);
        return [$leather];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
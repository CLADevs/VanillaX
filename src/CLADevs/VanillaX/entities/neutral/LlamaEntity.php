<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class LlamaEntity extends VanillaEntity{

    const NETWORK_ID = self::LLAMA;

    public $width = 0.9;
    public $height = 1.87;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Llama";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $leather = ItemFactory::get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $leather);
        return [$leather];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}
<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ElderGuardianEntity extends VanillaEntity{

    const NETWORK_ID = self::ELDER_GUARDIAN;

    public $width = 1.99;
    public $height = 1.99;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(80);
    }

    public function getName(): string{
        return "Elder Guardian";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO random fish
        $prismarine_shard = ItemFactory::get(ItemIds::PRISMARINE_SHARD, 0, 1);
        ItemHelper::applySetCount($prismarine_shard, 0, 2);
        ItemHelper::applyLootingEnchant($this, $prismarine_shard);
         
        $fish = ItemFactory::get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applyLootingEnchant($this, $fish);
         
        $prismarine_crystals = ItemFactory::get(ItemIds::PRISMARINE_CRYSTALS, 0, 1);
        ItemHelper::applyLootingEnchant($this, $prismarine_crystals);
         
        $sponge = ItemFactory::get(ItemIds::SPONGE, 0, 1);
        ItemHelper::applySetData($sponge, 1);
        return [$prismarine_shard, $fish, $prismarine_crystals, $sponge];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 10 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}
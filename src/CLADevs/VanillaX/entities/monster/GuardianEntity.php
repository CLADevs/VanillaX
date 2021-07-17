<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GuardianEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::GUARDIAN;

    public float $width = 0.85;
    public float $height = 0.85;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Guardian";
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 10 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $prismarine_shard = ItemFactory::getInstance()->get(ItemIds::PRISMARINE_SHARD, 0, 1);
        ItemHelper::applySetCount($prismarine_shard, 0, 2);
        ItemHelper::applyLootingEnchant($this, $prismarine_shard);
         
        $fish = ItemFactory::getInstance()->get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applyLootingEnchant($this, $fish);
         
        $prismarine_crystals = ItemFactory::getInstance()->get(ItemIds::PRISMARINE_CRYSTALS, 0, 1);
        ItemHelper::applyLootingEnchant($this, $prismarine_crystals);
        return [$prismarine_shard, $fish, $prismarine_crystals, ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1)];
    }
}
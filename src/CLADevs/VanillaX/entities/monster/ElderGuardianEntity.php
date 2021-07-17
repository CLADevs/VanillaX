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

class ElderGuardianEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ELDER_GUARDIAN;

    public float $width = 1.99;
    public float $height = 1.99;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(80);
    }

    public function getName(): string{
        return "Elder_Guardian";
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
         
        $sponge = ItemFactory::getInstance()->get(ItemIds::SPONGE, 0, 1);
        ItemHelper::applySetData($sponge, 1);
        return [$prismarine_shard, $fish, $prismarine_crystals, $sponge, ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1)];
    }
}
<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;

class CreeperEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::CREEPER;

    public float $width = 0.6;
    public float $height = 1.8;

    protected function initfdEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function isCharged(): bool{
        /** @var LongMetadataProperty $data */
        $data = $this->getNetworkProperties()->getAll()[EntityMetadataFlags::POWERED] ?? null;

        if($data !== null){
            $data = boolval($data->getValue());
        }
        return $data === null ? false : $data;
    }

    public function getName(): string{
        return "Creeper";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO, killed by skeleton drops music disc
        $gunpowder = ItemFactory::getInstance()->get(ItemIds::GUNPOWDER, 0, 1);
        ItemHelper::applySetCount($gunpowder, 0, 2);
        ItemHelper::applyLootingEnchant($this, $gunpowder);
        return [$gunpowder];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}
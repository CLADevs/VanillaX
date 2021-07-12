<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;

class GlowSquidEntity extends VanillaEntity{

    const NETWORK_ID = self::GLOW_SQUID;

    public $width = 0.95;
    public $height = 0.95;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Glow Squid";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO glow ink sac
//        $glow_ink_sac = ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1);
//        ItemHelper::applySetCount($glow_ink_sac, 1, 3);
//        ItemHelper::applySetData($glow_ink_sac, 0);
//        ItemHelper::applyLootingEnchant($this, $glow_ink_sac);
        return [];
    }
    
    public function getXpDropAmount(): int{
        return !$this->isBaby() && $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}
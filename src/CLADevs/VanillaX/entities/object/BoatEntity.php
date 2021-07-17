<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BoatEntity extends Entity{

    public float $width = 1.4;
    public float $height = 0.455;

    /** @var float */
    protected $gravity = 0.05;

    const NETWORK_ID = EntityIds::BOAT;

    public function kill(): void{
        if(GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            $this->getWorld()->dropItem($this->getPosition(), ItemFactory::getInstance()->get(ItemIds::BOAT)); //TODO boat block
        }
        parent::kill();
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}
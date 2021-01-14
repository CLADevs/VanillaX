<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use CLADevs\VanillaX\entities\traits\EntityInteractable;
use CLADevs\VanillaX\entities\traits\VillagerProfession;
use CLADevs\VanillaX\inventories\TradeInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\Player;

class VillagerEntity extends LivingEntity implements EntityInteractable{

    const NETWORK_ID = self::VILLAGER;

    public $width = 0.6;
    public $height = 1.9;

    const TAG_PROFESSION = "Profession";

    const UNEMPLOYED = 0;
    const NITWIT = 1;
    const ARMORER = 2;
    const BUTCHER = 3;
    const CARTOGRAPHER = 4;
    const CLERIC = 5;
    const FARMER = 6;
    const FISHERMAN = 7;
    const FLETCHER = 8;
    const LEATHERWORKER = 9;
    const LIBRARIAN = 10;
    const STONE_MASON = 11; //MASON IN JAVA
    const SHEPHERD = 12;
    const TOOLSMITH = 13;
    const WEAPONSMITH = 14;

    private ?VillagerProfession $profession = null;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        if($nbt->hasTag(self::TAG_PROFESSION)){
            $this->profession = VanillaX::getInstance()->getEntityManager()->getVillagerProfessionFor($nbt->getInt(self::TAG_PROFESSION));
        }
        if($this->profession === null){
            $this->profession = VanillaX::getInstance()->getEntityManager()->getVillagerProfessionFor(self::UNEMPLOYED);
        }
    }

    public function getName(): string{
        return "Villager";
    }

    public function saveNBT(): void{
        $this->namedtag->setInt(self::TAG_PROFESSION, $this->profession->getId());
        parent::saveNBT();
    }

    public function onInteract(Player $player, Item $item): void{
        $windowId = $player->addWindow(new TradeInventory($this));

        $pk = new UpdateTradePacket();
        $pk->windowId = $windowId;
        $pk->windowType = WindowTypes::TRADING;
        $pk->windowSlotCount = 1;
        $pk->tradeTier = 0;
        $pk->traderEid = $this->getId();
        $pk->playerEid = $player->getId();
        $pk->displayName = $this->profession->getName();
        $pk->isV2Trading = true;
        $pk->isWilling = false;
        $pk->offers = (new LittleEndianNBTStream())->write(ItemFactory::get(ItemIds::TNT)->nbtSerialize());
        $player->dataPacket($pk);
    }
}
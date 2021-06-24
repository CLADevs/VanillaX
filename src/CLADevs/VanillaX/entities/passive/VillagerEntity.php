<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\entities\utils\villager\VillagerTradeNBTStream;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class VillagerEntity extends VanillaEntity implements EntityInteractable{

    const TAG_PROFESSION = "Profession";
    const TAG_OFFER_BUFFER = "OfferBuffer";

    const NETWORK_ID = self::VILLAGER_V2;

    public $width = 0.6;
    public $height = 1.9;

    private ?Player $customer = null;
    private TradeInventory $inventory;
    private VillagerProfession $profession;

    private string $offerBuffer;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->inventory = new TradeInventory($this);
        if($this->namedtag->hasTag(self::TAG_PROFESSION, IntTag::class)){
            $profession = $this->namedtag->getInt(self::TAG_PROFESSION);
        }else{
            $profession = mt_rand(VillagerProfession::UNEMPLOYED, VillagerProfession::NITWIT);
        }
        $this->profession = VillagerProfession::getProfession($profession);

        if($hasOfferBuffer = $this->namedtag->hasTag(self::TAG_OFFER_BUFFER, StringTag::class)){
            $this->offerBuffer = $this->namedtag->getString(self::TAG_OFFER_BUFFER);
        }
        $this->setProfession($this->profession, VillagerProfession::BIOME_PLAINS, $hasOfferBuffer);
    }

    public function getName(): string{
        return "Villager";
    }

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function saveNBT(): void{
        $this->namedtag->setInt(self::TAG_PROFESSION, $this->profession->getId());
        $this->namedtag->setString(self::TAG_OFFER_BUFFER, $this->offerBuffer);
        parent::saveNBT();
    }

    public function setProfession(VillagerProfession $profession, int $biomeId = VillagerProfession::BIOME_PLAINS, bool $hasBuffer = false): void{
        $this->profession = $profession;

        if(!$hasBuffer && $profession->hasTrades()){
            $stream = new VillagerTradeNBTStream($profession);
            $stream->addOffer(VillagerProfession::TIER_NOVICE, $profession->getNovice());
            $stream->addOffer(VillagerProfession::TIER_APPRENTICE, $profession->getApprentice());
            $stream->addOffer(VillagerProfession::TIER_JOURNEYMAN, $profession->getJourneyman());
            $stream->addOffer(VillagerProfession::TIER_EXPERT, $profession->getExpert());
            $stream->addOffer(VillagerProfession::TIER_MASTER, $profession->getMaster());
            $stream->initialize();
            $this->offerBuffer = $stream->getBuffer();
        }
        $this->propertyManager->setInt(self::DATA_VARIANT, $profession->getId());
        $this->setBiomeType($biomeId);
    }

    public function getProfession(): VillagerProfession{
        return $this->profession;
    }

    public function setBiomeType(int $type): void{
        if($type < VillagerProfession::BIOME_PLAINS || $type > VillagerProfession::BIOME_TAIGA){
            $type = VillagerProfession::BIOME_PLAINS;
        }
        $this->propertyManager->setInt(self::DATA_MARK_VARIANT, $type);
    }

    public function getBiomeType(): int{
        return $this->propertyManager->getInt(self::DATA_MARK_VARIANT) ?? VillagerProfession::BIOME_PLAINS;
    }

    public function setCustomer(?Player $customer): void{
        $this->customer = $customer;
    }

    public function getCustomer(): ?Player{
        return $this->customer;
    }

    public function getOfferBuffer(): string{
        return $this->offerBuffer;
    }

    public function onInteract(EntityInteractResult $result): void{
        if($this->profession->hasTrades() && $this->customer === null){
            $player = $result->getPlayer();
            $this->customer = $player;
            VanillaX::getInstance()->getSessionManager()->get($player)->setTradingEntity($this);
            $player->addWindow($this->inventory, WindowTypes::TRADING);
        }
    }

    public function flagForDespawn(): void{
        parent::flagForDespawn();
        if($this->customer !== null && $this->customer->isOnline()){
            VanillaX::getInstance()->getSessionManager()->get($this->customer)->setTradingEntity(null);
        }
    }
}
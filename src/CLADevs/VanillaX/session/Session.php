<?php

namespace CLADevs\VanillaX\session;

use CLADevs\VanillaX\entities\projectile\TridentEntity;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\inventories\types\OffhandInventory;
use CLADevs\VanillaX\session\data\SessionGliding;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class Session{

    /** @var TridentEntity[] */
    private array $thrownTridents = [];

    private Player $player;
    private OffhandInventory $offHandInventory;
    private SessionGliding $gliding;
    private ?FakeBlockInventory $currentWindow = null;
    private ?VanillaEntity $ridingEntity = null;

    private int $entityId;

    public function __construct(Player $player){
        $this->player = $player;
        $this->offHandInventory = new OffhandInventory($player);
        $this->gliding = new SessionGliding();
        $this->entityId = $player->getId();
    }

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getOffHandInventory(): OffhandInventory{
        return $this->offHandInventory;
    }

    public function getRidingEntity(): ?VanillaEntity{
        return $this->ridingEntity;
    }

    public function setRidingEntity(?VanillaEntity $ridingEntity): void{
        if($ridingEntity !== null && $this->ridingEntity instanceof EntityRidable){
            $this->ridingEntity->onLeftRide($this->player);
        }
        $this->ridingEntity = $ridingEntity;
    }

    public function getCurrentWindow(): ?FakeBlockInventory{
        return $this->currentWindow;
    }

    public function setCurrentWindow(?FakeBlockInventory $currentWindow): void{
        $this->currentWindow = $currentWindow;
    }

    public function getGliding(): SessionGliding{
        return $this->gliding;
    }

    /**
     * @return TridentEntity[]
     */
    public function getThrownTridents(): array{
        return $this->thrownTridents;
    }

    public function addTrident(TridentEntity $entity): void{
        $this->thrownTridents[$entity->getId()] = $entity;
    }

    public function removeTrident(TridentEntity $entity): void{
        if(isset($this->thrownTridents[$entity->getId()])) unset($this->thrownTridents[$entity->getId()]);
    }

    /**
     * @param Player|Vector3 $player
     * @param string $sound
     * @param float $pitch
     * @param float $volume
     * @param bool $packet
     * @return DataPacket|null
     */
    public static function playSound($player, string $sound, float $pitch = 1, float $volume = 1, bool $packet = false): ?DataPacket{
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $player->x;
        $pk->y = $player->y;
        $pk->z = $player->z;
        $pk->pitch = $pitch;
        $pk->volume = $volume;
        if($packet){
            return $pk;
        }elseif($player instanceof Player){
            $player->dataPacket($pk);
        }
        return null;
    }
}
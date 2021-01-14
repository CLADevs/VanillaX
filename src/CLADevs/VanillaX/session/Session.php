<?php

namespace CLADevs\VanillaX\session;

use CLADevs\VanillaX\entities\projectile\TridentEntity;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class Session{

    private Player $player;

    private bool $gliding = false;
    private ?int $startGlideTime = null;
    private ?int $endGlideTime = null;

    private bool $inBoat = false;

    /** @var TridentEntity[] */
    private array $thrownTridents = [];

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function isGliding(): bool{
        return $this->gliding;
    }

    public function setGliding(bool $value = true): void{
        $this->gliding = $value;
        if($value){
            $this->startGlideTime = time();
        }else{
            $this->endGlideTime = time();
        }
    }

    public function getStartGlideTime(): ?int{
        return $this->startGlideTime;
    }

    public function getEndGlideTime(): ?int{
        return $this->endGlideTime;
    }

    public function isInBoat(): bool{
        return $this->inBoat;
    }

    public function setInBoat(bool $inBoat): void{
        $this->inBoat = $inBoat;
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

    public static function playSound(Player $player, string $sound, float $pitch = 1, float $volume = 1): void{
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $player->x;
        $pk->y = $player->y;
        $pk->z = $player->z;
        $pk->pitch = $pitch;
        $pk->volume = $volume;
        $player->dataPacket($pk);
    }
}
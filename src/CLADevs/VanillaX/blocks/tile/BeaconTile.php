<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\TileIds;
use CLADevs\VanillaX\inventories\actions\BeaconPaymentAction;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

class BeaconTile extends Spawnable{

    const TAG_PRIMARY = "primary";
    const TAG_SECONDARY = "secondary";

    const TILE_ID = TileIds::BEACON;
    const TILE_BLOCK = BlockLegacyIds::BEACON;

    private int $primary = 0;
    private int $secondary = 0;

    private BeaconInventory $inventory;

    /** @var BeaconPaymentAction[] */
    private array $queues = [];

    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);
        $this->inventory = new BeaconInventory(Position::fromObject($pos, $world));
    }

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_PRIMARY)) !== null){
            $this->primary = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_SECONDARY)) !== null){
            $this->secondary = $tag->getValue();
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setInt(self::TAG_PRIMARY, $this->primary);
        $nbt->setInt(self::TAG_SECONDARY, $this->secondary);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->writeSaveData($nbt);
    }

    public function isInQueue(Player $player): bool{
        return isset($this->queues[$player->getName()]);
    }

    public function addToQueue(Player $player, BeaconPaymentAction $action): void{
        $this->queues[$player->getName()] = $action;
    }

    public function removeFromQueue(Player $player): void{
        if($this->isInQueue($player)){
            unset($this->queues[$player->getName()]);
        }
    }

    public function getInventory(): BeaconInventory{
        return $this->inventory;
    }

    public function setPrimary(int $primary): void{
        $this->primary = $primary;
    }

    public function getPrimary(): int{
        return $this->primary;
    }

    public function setSecondary(int $secondary): void{
        $this->secondary = $secondary;
    }

    public function getSecondary(): int{
        return $this->secondary;
    }
}
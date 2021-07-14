<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\inventory\BlockInventory;
use pocketmine\block\inventory\BlockInventoryTrait;
use pocketmine\inventory\SimpleInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

class FakeBlockInventory extends SimpleInventory implements BlockInventory{
    use BlockInventoryTrait;

    private string $title;

    private int $defaultSize;
    private int $windowType;

    private ?Player $owner;
    private Block $block;

    /** @var null|callable */
    private $packetCallable;

    /**
     * FakeBlockInventory constructor.
     * @param Position $holder
     * @param int $size
     * @param Block|int $block
     * @param int $windowType
     * @param callable|null $packetCallable
     * @param Player|null $owner
     */
    public function __construct(Position $holder, int $size = 27, $block = BlockLegacyIds::CHEST, int $windowType = WindowTypes::CONTAINER, callable $packetCallable = null, ?Player $owner = null){
        parent::__construct($size);
        $holder->x = intval($holder->x);
        $holder->y = intval($holder->y);
        $holder->z = intval($holder->z);
        if(is_int($block)){
            $block = BlockFactory::getInstance()->get($block, 0);
        }
        $this->block = $block;
        $this->defaultSize = $size;
        $this->windowType = $windowType;
        $this->packetCallable = $packetCallable;
        $this->owner = $owner;
        $this->holder = $holder;
    }

    public function getOwner(): ?Player{
        return $this->owner;
    }

    public function getNetworkType(): int{
        return $this->windowType;
    }

    public function getDefaultSize(): int{
        return $this->defaultSize;
    }

    public function getName(): string{
        return "Inventory";
    }

    public function setTitle(string $title): void{
        $this->title = $title;
    }

    public function getTitle(): string{
        return $this->title;
    }

    public function onOpen(Player $who): void{
        if($this->block->getId() !== BlockLegacyIds::AIR){
            $block = clone $this->block;
            $this->sendBlock($who, $this->holder, $block->getId(), $block->getMeta());
        }
        $who->getNetworkSession()->sendDataPacket(ContainerOpenPacket::blockInvVec3($who->getNetworkSession()->getInvManager()->getCurrentWindowId(), $this->windowType, $this->holder));
        $who->getNetworkSession()->getInvManager()->syncContents($this);
        parent::onOpen($who);
    }

    public function onClose(Player $who): void{
        if($this->block->getId() !== BlockLegacyIds::AIR){
            $block = $who->getWorld()->getBlock($this->holder);
            $this->sendBlock($who, $this->holder, $block->getId(), $block->getMeta());
        }
        parent::onClose($who);
    }

    public function setPacketCallable(?callable $packetCallable): void{
        $this->packetCallable = $packetCallable;
    }

    /**
     * @param Player $player
     * @param ServerboundPacket $packet
     * @return bool, false will cancel event, true will let event continue
     */
    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
        $callable = $this->packetCallable;

        if($callable !== null){
            $callable($player, $packet);
        }
        return true;
    }

    public function sendBlock(Player $player, Vector3 $pos, int $blockId, int $blockMeta = 0): void{
        $block = BlockFactory::getInstance()->get($blockId ,$blockMeta);
        $pk = new UpdateBlockPacket();
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->blockRuntimeId = RuntimeBlockMapping::getInstance()->toRuntimeId($block->getFullId());
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
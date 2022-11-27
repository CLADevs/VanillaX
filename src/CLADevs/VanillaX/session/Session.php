<?php

namespace CLADevs\VanillaX\session;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\network\types\ItemStackInfo;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use ReflectionProperty;

class Session{

    private Player $player;
    private Random $random;
    private ?VanillaEntity $ridingEntity = null;
    private ?VillagerEntity $tradingEntity = null;

    private string $interactiveText = "";

    private int $entityId;
    private int $xpSeed;
    private int $nextItemStackId = 1;

    /** @var ItemStackInfo[][] */
    private array $itemStackInfos = [];
    /** @var int[] */
    private array $lastWindowIds = [];

    public function __construct(Player $player){
        $this->player = $player;
        $this->entityId = $player->getId();
        $this->random = new Random();

        $reflection = new ReflectionProperty(Player::class, "xpSeed");
        $reflection->setAccessible(true);
        $this->xpSeed = $reflection->getValue($player);
    }

    private function newItemStackId(): int{
        return $this->nextItemStackId++;
    }

    public function trackItemStack(Inventory $inventory, int $slotId, Item $item, ?int $itemStackRequestId): ItemStackInfo{
        $existing = $this->itemStackInfos[spl_object_id($inventory)][$slotId] ?? null;
        $typeConverter = TypeConverter::getInstance();
        $itemStack = $typeConverter->coreItemStackToNet($item);
        if($existing !== null && $existing->getItemStack()->equals($itemStack)){
            return $existing;
        }

        $info = new ItemStackInfo($itemStackRequestId, $item->isNull() ? 0 : $this->newItemStackId(), $itemStack);
        return $this->itemStackInfos[spl_object_id($inventory)][$slotId] = $info;
    }

    public function onContainerOpen(int $windowId): void{
        $located = $this->player->getNetworkSession()->getInvManager()->locateWindowAndSlot($windowId, -1);

        if($located === null){
            return;
        }
        [$inventory] = $located;

        if($inventory === null){
            return;
        }
        $this->lastWindowIds[$windowId] = spl_object_id($inventory);
    }

    public function onContainerClose(int $windowId): void{
        $id = $this->lastWindowIds[$windowId] ?? null;

        if($id !== null){
            unset($this->lastWindowIds[$windowId]);
            unset($this->itemStackInfos[$id]);
        }
    }

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function getInteractiveText(): string{
        return $this->interactiveText;
    }

    public function setInteractiveText(string $interactiveText): void{
        $this->interactiveText = $interactiveText;
        $this->player->getNetworkProperties()->setString(EntityMetadataProperties::INTERACTIVE_TAG, $interactiveText);
    }

    public function getPlayer(): Player{
        return $this->player;
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
    
    public function getTradingEntity(): ?VillagerEntity{
        return $this->tradingEntity;
    }

    public function setTradingEntity(?VillagerEntity $tradingEntity, bool $onQuit = false): void{
        if($onQuit && $this->tradingEntity !== null && $tradingEntity === null){
            $this->tradingEntity->setCustomer(null);
        }
        $this->tradingEntity = $tradingEntity;
    }

    public function getXpSeed(): int{
        return $this->xpSeed;
    }

    public function getRandom(): Random{
        return $this->random;
    }

    /**
     * @param Player|Vector3 $player
     * @param string $sound
     * @param float $pitch
     * @param float $volume
     * @param bool $packet
     * @return PlaySoundPacket|null
     */
    public static function playSound(Player|Vector3 $player, string $sound, float $pitch = 1, float $volume = 1, bool $packet = false): ?DataPacket{
        $pos = $player instanceof Player ? $player->getPosition() : $player;
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->pitch = $pitch;
        $pk->volume = $volume;
        if($packet){
            return $pk;
        }elseif($player instanceof Player){
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        return null;
    }
}
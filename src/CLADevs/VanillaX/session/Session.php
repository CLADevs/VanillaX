<?php

namespace CLADevs\VanillaX\session;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\projectile\TridentEntity;
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
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use ReflectionProperty;

class Session{

    private Player $player;
    private Random $random;
    private ?VanillaEntity $ridingEntity = null;
    private ?VillagerEntity $tradingEntity = null;

    /** @var TridentEntity[] */
    private array $thrownTridents = [];

    private string $interactiveText = "";

    private int $entityId;
    private int $xpSeed;
    private int $nextItemStackId = 1;

    /**
     * @var int[][]
     * @phpstan-var array<int, array<int, ItemStackInfo>>
     */
    private array $itemStackInfos = [];

    public function __construct(Player $player){
        $this->player = $player;
        $this->entityId = $player->getId();
        $this->random = new Random();

        $reflection = new ReflectionProperty(Player::class, "xpSeed");
        $reflection->setAccessible(true);
        $this->xpSeed = $reflection->getValue($player);
    }

    private function newItemStackId() : int{
        return $this->nextItemStackId++;
    }

    public function trackItemStack(Inventory $inventory, int $slotId, Item $item, ?int $itemStackRequestId) : ItemStackInfo{
        $existing = $this->itemStackInfos[spl_object_id($inventory)][$slotId] ?? null;
        $typeConverter = TypeConverter::getInstance();
        $itemStack = $typeConverter->coreItemStackToNet($item);
        if($existing !== null && $existing->getItemStack()->equals($itemStack)){
            return $existing;
        }

        $info = new ItemStackInfo($itemStackRequestId, $item->isNull() ? 0 : $this->newItemStackId(), $itemStack);
        return $this->itemStackInfos[spl_object_id($inventory)][$slotId] = $info;
    }

    public function wrapItemStack(Inventory $inventory, int $slotId, Item $item) : ItemStackWrapper{
        $info = $this->trackItemStack($inventory, $slotId, $item, null);
        return new ItemStackWrapper($info->getStackId(), $info->getItemStack());
    }

    public function matchItemStack(Inventory $inventory, int $slotId, int $itemStackId) : bool{
        $inventoryObjectId = spl_object_id($inventory);
        if(!isset($this->itemStackInfos[$inventoryObjectId])){
            $this->player->getNetworkSession()->getLogger()->debug("Attempted to match item preimage unsynced inventory " . get_class($inventory) . "#" . $inventoryObjectId);
            return false;
        }
        $info = $this->itemStackInfos[$inventoryObjectId][$slotId] ?? null;
        if($info === null){
            $this->player->getNetworkSession()->getLogger()->debug("Attempted to match item preimage for unsynced slot $slotId in " . get_class($inventory) . "#$inventoryObjectId that isn't synced");
            return false;
        }

        if(!($itemStackId < 0 ? $info->getRequestId() === $itemStackId : $info->getStackId() === $itemStackId)){
            $this->player->getNetworkSession()->getLogger()->debug(
                "Mismatched expected itemstack: " . get_class($inventory) . "#" . $inventoryObjectId . ", " .
                "slot: $slotId, expected: $itemStackId, actual: " . $info->getStackId() . ", last modified by request: " . ($info->getRequestId() ?? "none")
            );
            return false;
        }

        return true;
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
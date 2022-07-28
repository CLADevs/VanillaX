<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\event\inventory\itemstack\CraftItemStackEvent;
use CLADevs\VanillaX\event\inventory\itemstack\CreativeCreateItemStackEvent;
use CLADevs\VanillaX\event\inventory\itemstack\DestroyItemStackEvent;
use CLADevs\VanillaX\event\inventory\itemstack\DropItemStackEvent;
use CLADevs\VanillaX\event\inventory\itemstack\MoveItemStackEvent;
use CLADevs\VanillaX\event\inventory\itemstack\SwapItemStackEvent;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\inventories\types\AnvilInventory;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use CLADevs\VanillaX\inventories\types\SmithingInventory;
use CLADevs\VanillaX\inventories\utils\ContainerIds;
use CLADevs\VanillaX\VanillaX;
use Exception;
use pocketmine\block\inventory\CraftingTableInventory;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\inventory\CreativeInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerCraftingInventory;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\ItemStackResponsePacket;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\BeaconPaymentStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftingConsumeInputStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftingMarkSecondaryResultStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeAutoStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeOptionalStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CreativeCreateStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DeprecatedCraftingNonImplementedStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DeprecatedCraftingResultsStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DestroyStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DropStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\GrindstoneStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestSlotInfo;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\LabTableCombineStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\LoomStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\MineBlockStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\PlaceIntoBundleStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\PlaceStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\SwapStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\TakeFromBundleStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\TakeStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackresponse\ItemStackResponse;
use pocketmine\network\mcpe\protocol\types\inventory\stackresponse\ItemStackResponseContainerInfo;
use pocketmine\network\mcpe\protocol\types\inventory\stackresponse\ItemStackResponseSlotInfo;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\recipe\CraftingRecipeBlockName;
use pocketmine\network\mcpe\protocol\types\recipe\ShapedRecipe;
use pocketmine\Server;

class ItemStackRequestHandler{

    //From nukkit
    const CRAFTING_GRID_SMALL_OFFSET = 28;
    const CRAFTING_GRID_LARGE_OFFSET = 32;

    private ?Item $creativeOutput = null;

    /** @var ItemStackResponseContainerInfo[] */
    private array $containerInfo = [];

    public function __construct(private NetworkSession $session){
    }

    public function handleItemStackRequest(ItemStackRequestPacket $packet): bool{
        foreach($packet->getRequests() as $request){
            try {
                foreach($request->getActions() as $action){
//                    var_dump(get_class($action));
                    if($action instanceof TakeStackRequestAction){
                        $this->handleTake($action);
                    }else if($action instanceof PlaceStackRequestAction){
                        $this->handlePlace($action);
                    }else if($action instanceof SwapStackRequestAction){
                        $this->handleSwap($action);
                    }else if($action instanceof DropStackRequestAction){
                        $this->handleDrop($action);
                    }else if($action instanceof DestroyStackRequestAction){
                        $this->handleDestroy($action);
                    }else if($action instanceof CraftingConsumeInputStackRequestAction){
                        $this->handleCraftingConsumeInput($action);
                    }else if($action instanceof CraftingMarkSecondaryResultStackRequestAction){
                        $this->handleCraftingMarkSecondaryResult($action);
                    }else if($action instanceof PlaceIntoBundleStackRequestAction){
                        $this->handlePlaceIntoBundle($action);
                    }else if($action instanceof TakeFromBundleStackRequestAction){
                        $this->handleTakeFromBundle($action);
                    }else if($action instanceof LabTableCombineStackRequestAction){
                        $this->handleLabTableCombine($action);
                    }else if($action instanceof BeaconPaymentStackRequestAction){
                        $this->handleBeaconPayment($action);
                    }else if($action instanceof MineBlockStackRequestAction){
                        $this->handleMineBlock($action);
                    }else if($action instanceof CraftRecipeStackRequestAction){
                        $this->handleCraftRecipe($action);
                    }else if($action instanceof CraftRecipeAutoStackRequestAction){
                        $this->handleCraftRecipeAuto($action);
                    }else if($action instanceof CreativeCreateStackRequestAction){
                        $this->handleCreativeCreate($action);
                    }else if($action instanceof CraftRecipeOptionalStackRequestAction){
                        $this->handleCraftRecipeOptional($action, $request->getFilterStrings());
                    }else if($action instanceof GrindstoneStackRequestAction){
                        $this->handleGrindstone($action);
                    }else if($action instanceof LoomStackRequestAction){
                        $this->handleLoom($action);
                    }else if($action instanceof DeprecatedCraftingNonImplementedStackRequestAction){
                        $this->handleDeprecatedCraftingNonImplemented($action);
                    }else if($action instanceof DeprecatedCraftingResultsStackRequestAction){
                        $this->handleDeprecatedCraftingResults($action);
                    }
                }
                $this->acceptRequest($request->getRequestId());
            }catch (Exception $e){
                Server::getInstance()->getLogger()->logException($e);
                $this->rejectRequest($request->getRequestId());
                VanillaX::getInstance()->getLogger()->debug("Failed to handle ItemStackRequest for player '" . $this->session->getPlayer()->getName() . "': " . $e->getMessage());
            }
        }
        return true;
    }

    /**
     * @param TakeStackRequestAction $action
     * @throws Exception
     * Carries item on cursor
     */
    private function handleTake(TakeStackRequestAction $action): void{
        $this->move(MoveItemStackEvent::TYPE_TAKE, $action->getSource(), $action->getDestination(), $action->getCount());
    }

    /**
     * @param PlaceStackRequestAction $action
     * @throws Exception
     * Once its placed onto inventory slot from a cursor
     */
    private function handlePlace(PlaceStackRequestAction $action): void{
        $this->move(MoveItemStackEvent::TYPE_PLACE, $action->getSource(), $action->getDestination(), $action->getCount());
    }

    public function move(int $type, ItemStackRequestSlotInfo $source, ItemStackRequestSlotInfo $destination, int $count): void{
        if($this->session->getPlayer()->isCreative() && $source->getContainerId() === ContainerIds::ARMOR && $this->getItemFromStack($source)->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::BINDING))){
            return;
        }
        $ev = new MoveItemStackEvent($this->session->getPlayer(), $type, $count, $source, $destination);
        $ev->call();

        if($ev->isCancelled()){
            VanillaX::getInstance()->getLogger()->debug("Failed to execute MoveItemStack Type " . $type . ": Event Cancelled");
            return;
        }
        $source = $ev->getSource();
        $destination = $ev->getDestination();
        $count = $ev->getCount();
        $dest = $this->getItemFromStack($destination);

        if($source->getContainerId() === ContainerIds::CREATIVE_OUTPUT){
            if($this->creativeOutput === null){
                throw new Exception("Expected creative output to be created.");
            }
            $item = $this->creativeOutput;
        }else{
            $item = $this->getItemFromStack($source);
            $this->setItemInStack($source, $item->setCount($item->getCount() - $count));
        }
        if($dest->isNull()){
            $dest = (clone $item)->setCount(0);
        }
        $this->setItemInStack($destination, $dest->setCount($dest->getCount() + $count));
    }

    /**
     * @param SwapStackRequestAction $action
     * Switching slot with items
     */
    private function handleSwap(SwapStackRequestAction $action): void{
        $ev = new SwapItemStackEvent($this->session->getPlayer(), $action->getSlot1(), $action->getSlot2());
        $ev->call();

        if($ev->isCancelled()){
            VanillaX::getInstance()->getLogger()->debug("Failed to execute SwapItemStack: Event Cancelled");
            return;
        }
        $source = $ev->getSource();
        $dest = $ev->getDestination();
        $sourceItem = $this->getItemFromStack($source);
        $destItem = $this->getItemFromStack($dest);

        $this->setItemInStack($source, $destItem);
        $this->setItemInStack($dest, $sourceItem);
    }

    /**
     * @param DropStackRequestAction $action
     * Dropping item while inside the inventory
     */
    private function handleDrop(DropStackRequestAction $action): void{
        $player = $this->session->getPlayer();
        $ev = new DropItemStackEvent($player, $action->getSource(), $action->getCount(), $action->isRandomly());
        $ev->call();

        if($ev->isCancelled()){
            VanillaX::getInstance()->getLogger()->debug("Failed to execute DropItemStack: Event Cancelled");
            return;
        }
        $source = $ev->getSource();
        if($source->getContainerId() !== ContainerIds::CREATIVE_OUTPUT){
            $item = $this->getItemFromStack($source);
            $this->setItemInStack($source, VanillaItems::AIR());
        }else{
            $item = $this->creativeOutput;
        }
        $this->session->getPlayer()->dropItem($item);
    }

    /**
     * @param DestroyStackRequestAction $action
     * @throws Exception
     * Deleting items in creative mode by throwing it into creative inventory
     */
    private function handleDestroy(DestroyStackRequestAction $action): void{
        $source = $action->getSource();
        $player = $this->session->getPlayer();

        if(!$player->isCreative()){
            $handled = false;
            $inventory = $this->getInventory($source->getContainerId());

            if($inventory instanceof BeaconInventory){
                $handled = true;
            }
            if(!$handled){
                throw new Exception("received DestroyStackRequestAction while not being in creative");
            }
        }
        $ev = new DestroyItemStackEvent($player, $source);
        $ev->call();

        if($ev->isCancelled()){
            VanillaX::getInstance()->getLogger()->debug("Failed to execute DestroyItemStack: Event Cancelled");
            return;
        }
        $this->setItemInStack($ev->getSource(), VanillaItems::AIR());
    }

    /**
     * @param CraftingConsumeInputStackRequestAction $action
     * @throws Exception
     * Crafting input being reduced
     */
    private function handleCraftingConsumeInput(CraftingConsumeInputStackRequestAction $action): void{
        if($this->creativeOutput === null || $this->creativeOutput->isNull()){
            return;
        }
        $source = $action->getSource();
        $inventory = $this->getInventory($source->getContainerId());
        $index = $this->getIndexForInventory($source->getSlotId(), $inventory);

        if($inventory instanceof PlayerCraftingInventory){
            $crafting = $this->session->getPlayer()->getCraftingGrid();
        }else{
            $crafting = $inventory;
        }
        $item = $crafting->getItem($index);
        $this->setItemInStack($action->getSource(), $item->setCount($item->getCount() - $action->getCount()));
    }

    private function handleCraftingMarkSecondaryResult(CraftingMarkSecondaryResultStackRequestAction $action): void{
    }

    private function handlePlaceIntoBundle(PlaceIntoBundleStackRequestAction $action): void{
    }

    private function handleTakeFromBundle(TakeFromBundleStackRequestAction $action): void{
    }

    private function handleLabTableCombine(LabTableCombineStackRequestAction $action): void{
    }

    private function handleBeaconPayment(BeaconPaymentStackRequestAction $action): void{
        $player = $this->session->getPlayer();
        $currentInventory = $player->getCurrentWindow();

        if($currentInventory instanceof BeaconInventory){
            $currentInventory->onBeaconPayment($player, $action->getPrimaryEffectId(), $action->getSecondaryEffectId());
        }
    }

    private function handleMineBlock(MineBlockStackRequestAction $action): void{
    }

    /**
     * @param CraftRecipeStackRequestAction $action
     * @throws Exception
     * Crafting normally without using auto
     */
    private function handleCraftRecipe(CraftRecipeStackRequestAction $action): void{
        $netId = $action->getRecipeId();
        $player = $this->session->getPlayer();
        $currentInventory = $player->getCurrentWindow();

        if($currentInventory instanceof EnchantInventory){
            $this->creativeOutput = $currentInventory->getResultItem($player, $netId);
            return;
        }elseif($currentInventory instanceof SmithingInventory){
            $this->creativeOutput = $currentInventory->getResultItem($player, $netId);
            return;
        }
        $this->craft($netId);
    }

    /**
     * @param CraftRecipeAutoStackRequestAction $action
     * @throws Exception
     * Whenever you auto craft
     */
    private function handleCraftRecipeAuto(CraftRecipeAutoStackRequestAction $action): void{
        $this->craft($action->getRecipeId(), $action->getRepetitions(), true);
    }

    private function craft(int $netId, int $repetitions = 0, bool $auto = false): void{
        $recipe = InventoryManager::getInstance()->getRecipeByNetId($netId);
        if($recipe === null){
            throw new Exception("Failed to find recipe for id: " . $netId);
        }
        if(!$recipe instanceof ShapedRecipe && !$recipe instanceof ShapelessRecipe){
            throw new Exception("Recipe is not Shaped or Shapeless");
        }
        if($recipe->getBlockName() !== CraftingRecipeBlockName::CRAFTING_TABLE){
            throw new Exception("This recipe is not for crafting table");
        }
        $ev = new CraftItemStackEvent($this->session->getPlayer(), $recipe, TypeConverter::getInstance()->netItemStackToCore($recipe->getOutput()[0]), $repetitions, $auto);
        $ev->call();
        if($ev->isCancelled()){
            $ev->setResult(VanillaItems::AIR());
            VanillaX::getInstance()->getLogger()->debug("Failed to execute CraftItemStack: Event Cancelled");
        }

        $this->creativeOutput = $ev->getResult();
    }

    /**
     * @param CreativeCreateStackRequestAction $action
     * @throws Exception
     * Taking item from creative inventory into cursor
     */
    private function handleCreativeCreate(CreativeCreateStackRequestAction $action): void{
        $player = $this->session->getPlayer();

        if(!$player->isCreative()){
            throw new Exception("received CreativeCreateStackRequestAction while not being in creative");
        }
        $inventory = CreativeInventory::getInstance();
        $humanIndex = $action->getCreativeItemId();

        if($humanIndex > ($maxIndex = count($inventory->getAll()))){
            throw new Exception("received CreativeCreateStackRequestAction, expected index below $maxIndex, received $humanIndex.");
        }
        $ev = new CreativeCreateItemStackEvent($player, $inventory->getItem($humanIndex - 1), $humanIndex);
        $ev->call();

        if($ev->isCancelled()){
            VanillaX::getInstance()->getLogger()->debug("Failed to execute CreativeCreateItemStack: Event Cancelled");
            return;
        }
        $this->creativeOutput = $ev->getItem();
    }

    private function handleCraftRecipeOptional(CraftRecipeOptionalStackRequestAction $action, array $filterStrings): void{
        $player = $this->session->getPlayer();
        $currentInventory = $player->getCurrentWindow();

        if($currentInventory instanceof AnvilInventory){
            $this->creativeOutput = $currentInventory->getResultItem($player, $action->getFilterStringIndex(), $filterStrings);
        }
    }

    private function handleGrindstone(GrindstoneStackRequestAction $action): void{
    }

    private function handleLoom(LoomStackRequestAction $action): void{
    }

    private function handleDeprecatedCraftingNonImplemented(DeprecatedCraftingNonImplementedStackRequestAction $action): void{
    }

    private function handleDeprecatedCraftingResults(DeprecatedCraftingResultsStackRequestAction $action): void{

    }

    private function acceptRequest(int $requestId): void{
        $this->session->sendDataPacket(ItemStackResponsePacket::create([
            new ItemStackResponse(ItemStackResponse::RESULT_OK, $requestId, $this->containerInfo)
        ]));
        $this->containerInfo = [];
        $this->creativeOutput = null;
    }

    private function rejectRequest(int $requestId): void{
        $this->session->sendDataPacket(ItemStackResponsePacket::create([
            new ItemStackResponse(ItemStackResponse::RESULT_ERROR, $requestId, $this->containerInfo)
        ]));
        $this->containerInfo = [];
        $this->creativeOutput = null;
    }

    private function getInventory(int $id): Inventory{
        $inventory = ContainerIds::getInventory($id, $this->session->getPlayer());

        if(!$inventory){
            throw new Exception("Failed to find container with id of $id");
        }
        return $inventory;
    }

    private function getItemFromStack(ItemStackRequestSlotInfo $slotInfo): Item{
        $inventory = $this->getInventory($slotInfo->getContainerId());
        return $inventory->getItem($this->getIndexForInventory($slotInfo->getSlotId(), $inventory));
    }

    private function setItemInStack(ItemStackRequestSlotInfo $slotInfo, Item $item): void{
        $index = $slotInfo->getSlotId();
        $inventory = $this->getInventory($containerId = $slotInfo->getContainerId());

//        var_dump("Index $index " . $item->getName() . " " . $item->getCount() . " " . get_class($inventory));
        $this->containerInfo[] = new ItemStackResponseContainerInfo($containerId, [
            new ItemStackResponseSlotInfo(
                $index,
                $index,
                $item->getCount(),
                TypeConverter::getInstance()->coreItemStackToNet($item)->getId(),
                "",
                $item instanceof Durable ? $item->getDamage() : 0
            )
        ]);
        $inventory->setItem($this->getIndexForInventory($index, $inventory), $item);
    }

    private function getIndexForInventory(int $index, Inventory $inventory): int{
        if($index >= $inventory->getSize()){
            if($inventory instanceof CraftingTableInventory){
                $index -= self::CRAFTING_GRID_LARGE_OFFSET;
            }else if($inventory instanceof PlayerCraftingInventory){
                $index -= self::CRAFTING_GRID_SMALL_OFFSET;
            }
        }
        $slotMap = match(true){
            $inventory instanceof AnvilInventory => UIInventorySlotOffset::ANVIL,
            $inventory instanceof EnchantInventory => UIInventorySlotOffset::ENCHANTING_TABLE,
            $inventory instanceof BeaconInventory => [UIInventorySlotOffset::BEACON_PAYMENT => 0],
            $inventory instanceof SmithingInventory => [51 => 0, 52 => 2],
            default => null
        };
        if($slotMap !== null){
            $index = $slotMap[$index] ?? $index;
        }
        return $index;
    }
}
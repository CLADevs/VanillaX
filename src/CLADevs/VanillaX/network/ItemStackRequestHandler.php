<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\inventories\utils\ContainerIds;
use CLADevs\VanillaX\VanillaX;
use Exception;
use pocketmine\inventory\CreativeInventory;
use pocketmine\inventory\Inventory;
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

class ItemStackRequestHandler{

    private ?Item $creativeItem = null;

    /** @var ItemStackResponseContainerInfo */
    private array $containerInfo = [];

    public function __construct(private NetworkSession $session){
    }

    public function handleItemStackRequest(ItemStackRequestPacket $packet): bool{
        foreach($packet->getRequests() as $request){
            try {
                foreach($request->getActions() as $action){
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
                        $this->handleCraftRecipeOptional($action);
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
                $this->rejectRequest($request->getRequestId());
                VanillaX::getInstance()->getLogger()->debug("Failed to handle ItemStackRequest for player '" . $this->session->getPlayer()->getName() . "': " . $e->getMessage());
            }
        }
        return true;
    }

    private function handleTake(TakeStackRequestAction $action): void{
        $this->move($action->getSource(), $action->getDestination(), $action->getCount());
    }

    private function handlePlace(PlaceStackRequestAction $action): void{
        $this->move($action->getSource(), $action->getDestination(), $action->getCount());
    }

    public function move(ItemStackRequestSlotInfo $source, ItemStackRequestSlotInfo $destination, int $count): void{
        $dest = $this->getItemFromStack($destination);

        if($source->getContainerId() === ContainerIds::CREATIVE_OUTPUT){
            if($this->creativeItem === null){
                return;
            }
            $item = $this->creativeItem;
        }else{
            $item = $this->getItemFromStack($source);
            $this->setItemInStack($source, $item->setCount($item->getCount() - $count));
        }
        if($dest->isNull()){
            $dest = (clone $item)->setCount(0);
        }
        $this->setItemInStack($destination, $dest->setCount($dest->getCount() + $count));
    }

    private function handleSwap(SwapStackRequestAction $action): void{
        $source = $action->getSlot1();
        $dest = $action->getSlot2();
        $sourceItem = $this->getItemFromStack($source);
        $destItem = $this->getItemFromStack($dest);

        $this->setItemInStack($source, $destItem);
        $this->setItemInStack($dest, $sourceItem);
    }

    private function handleDrop(DropStackRequestAction $action): void{
        $item = $this->getItemFromStack($action->getSource());
        $this->setItemInStack($action->getSource(), VanillaItems::AIR());
        $this->session->getPlayer()->dropItem($item);
    }

    private function handleDestroy(DestroyStackRequestAction $action): void{
        if(!$this->session->getPlayer()->isCreative()){
            throw new Exception("received DestroyStackRequestAction while not being in creative");
        }
        $this->setItemInStack($action->getSource(), VanillaItems::AIR());
    }

    private function handleCraftingConsumeInput(CraftingConsumeInputStackRequestAction $action): void{
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
    }

    private function handleMineBlock(MineBlockStackRequestAction $action): void{
    }

    private function handleCraftRecipe(CraftRecipeStackRequestAction $action): void{
    }

    private function handleCraftRecipeAuto(CraftRecipeAutoStackRequestAction $action): void{
    }

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
        $this->creativeItem = $inventory->getItem($humanIndex - 1);
    }

    private function handleCraftRecipeOptional(CraftRecipeOptionalStackRequestAction $action): void{
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
        $this->creativeItem = null;
    }

    private function rejectRequest(int $requestId): void{
        $this->session->sendDataPacket(ItemStackResponsePacket::create([
            new ItemStackResponse(ItemStackResponse::RESULT_ERROR, $requestId, $this->containerInfo)
        ]));
        $this->containerInfo = [];
        $this->creativeItem = null;
    }

    private function getInventory(int $id): Inventory{
        $inventory = ContainerIds::getInventory($id, $this->session->getPlayer());

        if(!$inventory){
            throw new Exception("Failed to find container with id of $id");
        }
        return $inventory;
    }

    private function getItemFromStack(ItemStackRequestSlotInfo $slotInfo): Item{
        return $this->getInventory($slotInfo->getContainerId())->getItem($slotInfo->getSlotId());
    }

    private function setItemInStack(ItemStackRequestSlotInfo $slotInfo, Item $item): void{
        $index = $slotInfo->getSlotId();
        $inventory = $this->getInventory($containerId = $slotInfo->getContainerId());

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
        $inventory->setItem($index, $item);
    }

}
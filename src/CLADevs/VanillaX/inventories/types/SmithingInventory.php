<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\event\inventory\UpgradeItemEvent;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\VanillaX;
use Exception;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\TemporaryInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SmithingInventory extends FakeBlockInventory implements TemporaryInventory, RecipeInventory{

    const BLOCK_NAME = "smithing_table";

    const SLOT_INPUT = 0;
    const SLOT_MATERIAL = 1;

    public function __construct(Position $holder){
        parent::__construct($holder, 3, BlockLegacyIds::AIR, WindowTypes::SMITHING_TABLE);
    }

    public function getResultItem(Player $player, int $netId): ?Item{
        $recipe = InventoryManager::getInstance()->getRecipeByNetId($netId);
        if($recipe === null){
            throw new Exception("Failed to find recipe for smithing table with id of: " . $netId);
        }
        if(!$recipe instanceof ShapelessRecipe){
            throw new Exception("smithing table recipe should be shapeless for id: " . $netId);
        }
        if($recipe->getBlockName() !== self::BLOCK_NAME){
            throw new Exception("This recipe is not for crafting table");
        }
        $input = $this->getItem(self::SLOT_INPUT);
        $material = $this->getItem(self::SLOT_MATERIAL);
        $result = TypeConverter::getInstance()->netItemStackToCore($recipe->getOutputs()[0]);

        if($input->hasNamedTag()){
            foreach($input->getNamedTag()->getValue() as $key => $tag){
                $result->getNamedTag()->setTag($key, $tag);
            }
        }
        if($input->hasCustomName()){
            $result->setCustomName($input->getCustomName());
        }
        if($input->hasEnchantments()){
            foreach($input->getEnchantments() as $enchantment){
                $result->addEnchantment($enchantment);
            }
        }
        $ev = new UpgradeItemEvent($player, $recipe, $input, $material, $result);
        $ev->call();
        if($ev->isCancelled()){
            $ev->setResult(VanillaItems::AIR());
            VanillaX::getInstance()->getLogger()->debug("Failed to upgrade item: Event Cancelled");
        }

        return $ev->getResult();
    }
}
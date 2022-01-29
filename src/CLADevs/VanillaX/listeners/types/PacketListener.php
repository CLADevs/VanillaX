<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use const pocketmine\BEDROCK_DATA_PATH;

class PacketListener implements Listener{

    public function onInventoryTransaction(InventoryTransactionEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleInventoryTransaction($event);
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void{
        if(!$event->isCancelled()){
            foreach($event->getPackets() as $packet){
                switch($packet::NETWORK_ID){
                    case ProtocolInfo::AVAILABLE_COMMANDS_PACKET:
                        if($packet instanceof AvailableCommandsPacket) $this->handleCommandEnum($packet);
                        break;
                    case ProtocolInfo::CRAFTING_DATA_PACKET:
                        if($packet instanceof CraftingDataPacket) $this->handleCraftingData($packet);
                        break;
                }
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        if(!$event->isCancelled() && ($player = $event->getOrigin()->getPlayer()) !== null){
            $packet = $event->getPacket();
            $window = $player->getCurrentWindow();

            if($window instanceof FakeBlockInventory && !$window->handlePacket($player, $packet)){
                $event->cancel();
            }
        }
    }

    /**
     * @param AvailableCommandsPacket $packet
     * Modifies enums for commands, arguments you see once you type /weather
     * are modified through this packet. '<clear: rain: thunder> [duration: int]'
     */
    private function handleCommandEnum(AvailableCommandsPacket $packet): void{
        foreach(VanillaX::getInstance()->getCommandManager()->getCommands() as $key => $command){
            if(($arg = $command->getCommandArg()) !== null && ($command = $packet->commandData[strtolower($key)] ?? null) !== null){
                $command->flags = $arg->getFlags();
                $command->permission = $arg->getPermission();
                $command->overloads = $arg->getOverload();
            }
        }
    }

    /**
     * @param CraftingDataPacket $packet
     * called whenever player joins to send recipes for brewing, crafting, etc
     */
    private function handleCraftingData(CraftingDataPacket $packet): void{
        $manager = InventoryManager::getInstance();
        $translator = ItemTranslator::getInstance();
        $recipes = json_decode(file_get_contents(BEDROCK_DATA_PATH . "recipes.json"), true);

        $potionTypeRecipes = [];
        foreach($recipes["potion_type"] as $recipe){
            [$inputNetId, $inputNetDamage] = $translator->toNetworkId($recipe["input"]["id"], $recipe["input"]["damage"] ?? 0);
            [$ingredientNetId, $ingredientNetDamage] = $translator->toNetworkId($recipe["ingredient"]["id"], $recipe["ingredient"]["damage"] ?? 0);
            [$outputNetId, $outputNetDamage] = $translator->toNetworkId($recipe["output"]["id"], $recipe["output"]["damage"] ?? 0);
            $potion = new PotionTypeRecipe($inputNetId, $inputNetDamage, $ingredientNetId, $ingredientNetDamage, $outputNetId, $outputNetDamage);
            $packet->potionTypeRecipes[] = $potion;
            $potion = $manager->internalPotionTypeRecipe(clone $potion);
            $potionTypeRecipes[$manager->hashPotionType($potion)] = $potion;
        }

        $potionContainerRecipes = [];
        foreach($recipes["potion_container_change"] as $recipe){
            $inputNetId = $translator->toNetworkId($recipe["input_item_id"], 0)[0];
            $ingredientNetId = $translator->toNetworkId($recipe["ingredient"]["id"], 0)[0];
            $outputNetId = $translator->toNetworkId($recipe["output_item_id"], 0)[0];
            $potion = new PotionContainerChangeRecipe($inputNetId, $ingredientNetId, $outputNetId);
            $packet->potionContainerRecipes[] = $potion;
            $potion = $manager->internalPotionContainerRecipe(clone $potion);
            $potionContainerRecipes[$manager->hashPotionContainer($potion)] = $potion;
        }

        InventoryManager::getInstance()->setPotionTypeRecipes($potionTypeRecipes);
        InventoryManager::getInstance()->setPotionContainerRecipes($potionContainerRecipes);
    }
}
<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\inventory\TemporaryInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SmithingInventory extends FakeBlockInventory implements TemporaryInventory{

    public function __construct(Position $holder){
        parent::__construct($holder, 3, BlockLegacyIds::AIR, WindowTypes::SMITHING_TABLE);
    }

    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
        return true;
    }

    public function onSuccess(Player $player, Item $item): void{
    }

    public function handleSlotChange(): void{
    }

    public function handleResult(): void{
    }
}
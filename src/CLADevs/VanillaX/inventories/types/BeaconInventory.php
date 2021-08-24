<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

class BeaconInventory extends FakeBlockInventory{

    public function __construct(Position $holder){
        parent::__construct($holder, 1, BlockLegacyIds::AIR, WindowTypes::BEACON);
    }

    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
        if($packet instanceof BlockActorDataPacket){
            $nbt = $packet->namedtag->getRoot();

            if($nbt instanceof CompoundTag){
                $id = $nbt->getTag("id");

                if($id instanceof StringTag && $id->getValue() === "Beacon"){
                    $tile = $player->getWorld()->getTileAt($nbt->getInt("x"), $nbt->getInt("y"), $nbt->getInt("z"));

                    if($tile instanceof BeaconTile){
                        $tile->setPrimary($nbt->getInt(BeaconTile::TAG_PRIMARY));
                        $tile->setSecondary($nbt->getInt(BeaconTile::TAG_SECONDARY));
                        $tile->getPosition()->getWorld()->scheduleDelayedBlockUpdate($tile->getPosition(), 20);
                    }
                    return false;
                }
            }
        }
        return true;
    }
}
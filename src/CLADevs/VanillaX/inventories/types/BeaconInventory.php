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
            $root = $packet->nbt->getRoot();

            if($root instanceof CompoundTag){
                $id = $root->getTag("id");

                if($id instanceof StringTag && $id->getValue() === "Beacon"){
                    $tile = $player->getWorld()->getTileAt($root->getInt("x"), $root->getInt("y"), $root->getInt("z"));

                    if($tile instanceof BeaconTile && $tile->isInQueue($player)){
                        $tile->setPrimary($root->getInt(BeaconTile::TAG_PRIMARY));
                        $tile->setSecondary($root->getInt(BeaconTile::TAG_SECONDARY));
                        $tile->getPosition()->getWorld()->scheduleDelayedBlockUpdate($tile->getPosition(), 20);
                        $tile->removeFromQueue($player);
                    }
                    return false;
                }
            }
        }
        return true;
    }
}
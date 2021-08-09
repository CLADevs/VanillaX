<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use Exception;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;

class BeaconBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BEACON, 0, null, BeaconTile::class),"Beacon", new BlockBreakInfo(3));
    }

    /**
     * @param Item $item
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     * @return bool
     * @throws Exception
     */
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player instanceof Player){
            $player->setCurrentWindow(new BeaconInventory($this->pos));
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

        if(!$tile instanceof BeaconTile){
            return;
        }
        $primary = $tile->getPrimary();
        $secondary = $tile->getSecondary();

        if($primary === 0 && $secondary === 0){
            return;
        }
        $level = 0;

        for($i = 1; $i <= 4; $i++){
            if(!$this->isLevelValid($i)){
                break;
            }
            $level++;
        }
        if($level !== 0){
            $radius = 10 + (10 * $level);
            $effectDuration = 20 * (9 + (2 * $level));

            foreach(Server::getInstance()->getOnlinePlayers() as $p){
                if($p->getPosition()->distance($this->getPos()) < $radius){
                    foreach([$primary, $secondary] as $effectId){
                        if($effectId !== 0){
                            $p->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId($effectId), $effectDuration));
                        }
                    }
                }
            }
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 20 * 3);
    }

    /**
     * @param int $level
     * @return bool
     * @throws Exception
     */
    public function isLevelValid(int $level): bool{
        if($level > 4 || $level < 1){
            throw new Exception("Beacon level must be 1, 2, 3 or 4, received $level");
        }

        $i = 0;
        for($x = -$level; $x <= $level; $x++){
            for($z = -$level; $z <= $level; $z++){
                $block = $this->pos->getWorld()->getBlock($this->pos->add($x, 0, $z)->subtract(0, $level, 0));

                if(!in_array($block->getId(), [BlockLegacyIds::IRON_BLOCK, BlockLegacyIds::GOLD_BLOCK, BlockLegacyIds::DIAMOND_BLOCK, BlockLegacyIds::EMERALD_BLOCK, BlockVanilla::NETHERITE_BLOCK])){
                    return false;
                }
                $i++;
            }
        }
        return true;
    }
}
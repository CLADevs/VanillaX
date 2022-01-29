<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\utils\entity\CustomRegisterEntityNamesTrait;
use CLADevs\VanillaX\utils\entity\CustomRegisterEntityTrait;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use Closure;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\ByteTag;
use UnexpectedValueException;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\object\Painting;
use pocketmine\entity\object\PaintingMotive;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\World;

class PaintingEntity extends Painting implements CustomRegisterEntityTrait , CustomRegisterEntityNamesTrait{

    const NETWORK_ID = EntityIds::PAINTING;

    public static function getRegisterClosure(): Closure{
        return function(World $world, CompoundTag $nbt): PaintingEntity{
            $motive = PaintingMotive::getMotiveByName($nbt->getString("Motive"));
            if($motive === null){
                throw new UnexpectedValueException("Unknown painting motive");
            }
            $blockIn = new Vector3($nbt->getInt("TileX"), $nbt->getInt("TileY"), $nbt->getInt("TileZ"));
            if(($directionTag = $nbt->getTag("Direction")) instanceof ByteTag){
                $facing = Painting::DATA_TO_FACING[$directionTag->getValue()] ?? Facing::NORTH;
            }elseif(($facingTag = $nbt->getTag("Facing")) instanceof ByteTag){
                $facing = Painting::DATA_TO_FACING[$facingTag->getValue()] ?? Facing::NORTH;
            }else{
                throw new UnexpectedValueException("Missing facing info");
            }

            return new PaintingEntity(EntityDataHelper::parseLocation($nbt, $world), $blockIn, $facing, $motive, $nbt);
        };
    }

    public static function getRegisterSaveNames(): array{
        return ['Painting', 'minecraft:painting'];
    }

    public static function getSaveId(): ?int{
        return EntityLegacyIds::PAINTING;
    }

    public function kill(): void{
        if(!$this->isAlive()){
            return;
        }
        parent::kill();

        $drops = true;

        if($this->lastDamageCause instanceof EntityDamageByEntityEvent){
            $killer = $this->lastDamageCause->getDamager();
            if($killer instanceof Player && $killer->isCreative()){
                $drops = false;
            }
        }

        if($drops && GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            $this->getWorld()->dropItem($this->getPosition(), ItemFactory::getInstance()->get(ItemIds::PAINTING));
        }
        $this->getWorld()->addParticle($this->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle(BlockFactory::getInstance()->get(BlockLegacyIds::PLANKS)));
    }

    public static function canRegister(): bool{
        return true;
    }
}
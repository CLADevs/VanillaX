<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\object\PaintingEntity;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\object\PaintingMotive;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use UnexpectedValueException;

class EntityManager{
    use SingletonTrait;

    /** @var string[] */
    private array $entities = [];

    public function __construct(){
        self::setInstance($this);
    }

    public function startup(): void{
        VillagerProfession::init();
        if(VanillaX::getInstance()->getConfig()->get("mobs", true)){
            foreach(["object", "projectile", "boss", "passive", "neutral", "monster"] as $path){
                Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, function (string $namespace): void{
                    if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                        $this->registerEntity($namespace, [$namespace::NETWORK_ID]);
                    }
                });
            }
            $this->registerEntity(PaintingEntity::class);
        }
    }

    public function registerEntity(string $namespace, array $saveNames = []): void{
        $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);

        if(in_array($namespace::NETWORK_ID, $disabledMobs)){
           return;
        }
        $this->entities[$namespace::NETWORK_ID] = $namespace;
        if($namespace::NETWORK_ID === PaintingEntity::NETWORK_ID){
            EntityFactory::getInstance()->register(PaintingEntity::class, function(World $world, CompoundTag $nbt): PaintingEntity{
                $motive = PaintingMotive::getMotiveByName($nbt->getString("Motive"));
                if($motive === null){
                    throw new UnexpectedValueException("Unknown painting motive");
                }
                $blockIn = new Vector3($nbt->getInt("TileX"), $nbt->getInt("TileY"), $nbt->getInt("TileZ"));
                if(($directionTag = $nbt->getTag("Direction")) instanceof ByteTag){
                    $facing = PaintingEntity::DATA_TO_FACING[$directionTag->getValue()] ?? Facing::NORTH;
                }elseif(($facingTag = $nbt->getTag("Facing")) instanceof ByteTag){
                    $facing = PaintingEntity::DATA_TO_FACING[$facingTag->getValue()] ?? Facing::NORTH;
                }else{
                    throw new UnexpectedValueException("Missing facing info");
                }

                return new PaintingEntity(EntityDataHelper::parseLocation($nbt, $world), $blockIn, $facing, $motive, $nbt);
            }, ['Painting', 'minecraft:painting'], EntityLegacyIds::PAINTING);
            return;
        }
        EntityFactory::getInstance()->register($namespace, function(World $world, CompoundTag $nbt)use($namespace): Entity{
            return new $namespace(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $saveNames);
    }

    /**
     * @param string $entity
     * @return string|null
     * returns namespace of the entity or null if not found
     */
    public function getEntity(string $entity): ?string{
        return $this->entities[$entity] ?? null;
    }

    /**
     * @return string[]
     */
    public function getEntities(): array{
        return $this->entities;
    }
}
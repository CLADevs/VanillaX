<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\monster\PiglinBruteEntity;
use CLADevs\VanillaX\entities\neutral\GoatEntity;
use CLADevs\VanillaX\entities\object\PaintingEntity;
use CLADevs\VanillaX\entities\passive\AxolotlEntity;
use CLADevs\VanillaX\entities\passive\GlowSquidEntity;
use CLADevs\VanillaX\entities\utils\EntityIdentifierX;
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
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use ReflectionClass;
use UnexpectedValueException;

class EntityManager{
    use SingletonTrait;

    /** @var EntityIdentifierX[] */
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

    private function initializeEntityIds(string $namespace): void{
        $entityIds = (new ReflectionClass(EntityIds::class))->getConstants();
        $entityLegacyIds = (new ReflectionClass(EntityLegacyIds::class))->getConstants();
        $networkId = $namespace::NETWORK_ID;
        $key = array_search($networkId, $entityIds);
        $id = $entityLegacyIds[$key] ?? null;

        if($id === null){
            switch($networkId){
                case GlowSquidEntity::NETWORK_ID:
                    $key = "GLOW_SQUID";
                    $id = VanillaEntity::GLOW_SQUID;
                    break;
                case GoatEntity::NETWORK_ID:
                    $key = "GOAT";
                    $id = VanillaEntity::GOAT;
                    break;
                case AxolotlEntity::NETWORK_ID:
                    $key = "AXOLOTL";
                    $id = VanillaEntity::AXOLOTL;
                    break;
                case PiglinBruteEntity::NETWORK_ID:
                    $key = "PIGLIN_BRUTE";
                    $id = VanillaEntity::PIGLIN_BRUTE;
                    break;
            }
        }

        if($id !== null && $key !== false){
            $entityName = [];
            foreach(explode("_", $key) as $value){
                $entityName[] = ucfirst(strtolower($value));
            }
            $entityName = implode(" ", $entityName);
            $this->entities[$networkId] = new EntityIdentifierX($networkId, $entityName, $namespace, $id);
        }
    }

    public function registerEntity(string $namespace, array $saveNames = []): void{
        $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);

        if(in_array($namespace::NETWORK_ID, $disabledMobs)){
           return;
        }
        $this->initializeEntityIds($namespace);

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
     * @return EntityIdentifierX|null
     * returns namespace of the entity or null if not found
     */
    public function getEntity(string $entity): ?string{
        return $this->entities[$entity] ?? null;
    }

    /**
     * @return EntityIdentifierX[]
     */
    public function getEntities(): array{
        return $this->entities;
    }
}
<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\monster\PiglinBruteEntity;
use CLADevs\VanillaX\entities\neutral\GoatEntity;
use CLADevs\VanillaX\entities\object\PaintingEntity;
use CLADevs\VanillaX\entities\passive\AxolotlEntity;
use CLADevs\VanillaX\entities\passive\GlowSquidEntity;
use CLADevs\VanillaX\entities\passive\StriderEntity;
use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\EntityIdentifierX;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\utils\entity\CustomRegisterEntityNamesTrait;
use CLADevs\VanillaX\utils\entity\CustomRegisterEntityTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use Closure;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use ReflectionClass;

class EntityManager{
    use SingletonTrait;

    /** @var EntityIdentifierX[] */
    private array $entities = [];

    public function __construct(){
        self::setInstance($this);
    }

    public function startup(): void{
        VillagerProfession::init();

        if(VanillaX::getInstance()->getConfig()->get("entities", true)){
            $pathList = ["object", "projectile"];

            if(VanillaX::getInstance()->getConfig()->get("mobs", true)){
                $pathList = array_merge($pathList, ["boss", "passive", "neutral", "monster"]);
            }
            foreach($pathList as $path){
                Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, function (string $namespace)use($path): void{
                    $implements = class_implements($namespace);

                    if(!isset($implements[NonAutomaticCallItemTrait::class])){
                        $closure = null;
                        $saveNames = [$namespace::NETWORK_ID];
                        $saveId = null;

                        if(isset($implements[CustomRegisterEntityTrait::class])){
                            /** @var CustomRegisterEntityTrait $namespace */
                            $closure = $namespace::getRegisterClosure();
                        }
                        if(isset($implements[CustomRegisterEntityNamesTrait::class])){
                            /** @var CustomRegisterEntityNamesTrait $namespace */
                            $saveNames = $namespace::getRegisterSaveNames();
                            $saveId = $namespace::getSaveId();
                        }
                        $this->registerEntity($namespace, $path, $saveNames, $saveId, $closure);
                    }
                });
            }
        }
    }

    private function initializeEntityIds(string $namespace, string $path): void{
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
                case StriderEntity::NETWORK_ID:
                    $key = "STRIDER";
                    $id = VanillaEntity::STRIDER;
                    break;
                case VillagerEntity::NETWORK_ID:
                    $key = "VILLAGER";
                    $id = VanillaEntity::VILLAGER_V2;
                    break;
            }
        }

        if($id !== null && $key !== false){
            $entityName = [];
            foreach(explode("_", $key) as $value){
                $entityName[] = ucfirst(strtolower($value));
            }
            $entityName = implode(" ", $entityName);
            $this->entities[$networkId] = new EntityIdentifierX($networkId, $entityName, $namespace, $path, $id);
        }
    }

    public function registerEntity(string $namespace, string $path = "none", array $saveNames = [], ?int $saveId = null, ?Closure $closure = null): void{
        $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);

        if(in_array($namespace::NETWORK_ID, $disabledMobs)){
           return;
        }
        $this->initializeEntityIds($namespace, $path);

        EntityFactory::getInstance()->register($namespace, $closure ? $closure : function(World $world, CompoundTag $nbt)use($namespace): Entity{
            return new $namespace(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $saveNames, $saveId);
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
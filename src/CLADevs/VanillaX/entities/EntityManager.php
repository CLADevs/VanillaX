<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\utils\EntityCustomSaveNames;
use CLADevs\VanillaX\entities\utils\EntityInfo;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\entities\utils\EntityCustomRegisterClosure;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use Closure;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use const pocketmine\BEDROCK_DATA_PATH;

class EntityManager{
    use SingletonTrait;

    /** @var int[] */
    private array $entityIdMap;
    /** @var EntityInfo[] */
    private array $entityInfoMap = [];

    public function __construct(){
        self::setInstance($this);
        $this->entityIdMap = json_decode(file_get_contents(BEDROCK_DATA_PATH . "/entity_id_map.json"), true);
    }

    public function startup(): void{
        VillagerProfession::init();

        foreach(["object", "projectile", "boss", "passive", "neutral", "monster"] as $type){
            Utils::callDirectory("entities/$type", function (string $namespace)use($type): void{
                $implements = class_implements($namespace);

                if(!isset($implements[NonAutomaticCallItemTrait::class])){
                    $names = [$namespace::getNetworkTypeId()];
                    $closure = null;

                    if(isset($implements[EntityCustomRegisterClosure::class])){
                        /** @var EntityCustomRegisterClosure $namespace */
                        $closure = $namespace::getRegisterClosure();
                    }
                    if(isset($implements[EntityCustomSaveNames::class])){
                        /** @var EntityCustomSaveNames $namespace */
                        $names = array_merge($namespace::getSaveNames(), $names);
                    }
                    /** @var VanillaEntity $namespace */
                    $this->registerEntity($namespace, $type, $names, null, $closure);
                }
            });
        }
    }

    public function registerEntity(string $namespace, string $type = "none", array $saveNames = [], ?int $legacyId = null, ?Closure $closure = null): void{
       /** @var VanillaEntity $namespace */
        if(!$namespace::canRegister()){
            return;
        }
        EntityFactory::getInstance()->register($namespace, $closure ?: function(World $world, CompoundTag $nbt)use($namespace): Entity{
            return new $namespace(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $saveNames, $legacyId);

        $name = $namespace::getNetworkTypeId();
        $displayName = [];
        foreach(explode("_", $name) as $value){
            $displayName[] = ucfirst(strtolower($value));
        }
        $displayName = implode(" ", $displayName);
        $legacyId = $legacyId ?? $this->entityIdMap[$name];
        $legacyId = $legacyId == null ? null : ($legacyId === VanillaEntity::VILLAGER_V2 ? EntityLegacyIds::VILLAGER : $legacyId);

        $info = new EntityInfo($type, $name, str_replace("minecraft:", "", $displayName), $namespace, $legacyId);
        $this->entityInfoMap[$name] = $info;

        if($legacyId !== null){
            $this->entityInfoMap[$legacyId === VanillaEntity::VILLAGER_V2 ? EntityLegacyIds::VILLAGER : $legacyId] = $info;
        }
    }

    /**
     * @param string|int $entity, string = vanilla name, int = legacy id
     * @return EntityInfo|null
     */
    public function getEntityInfo(string|int $entity): ?EntityInfo{
        return $this->entityInfoMap[$entity] ?? null;
    }

    /**
     * @return EntityInfo[]
     */
    public function getEntityInfoMap(): array{
        return $this->entityInfoMap;
    }
}
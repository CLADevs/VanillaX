<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\types\PigEntity;

class EntityManager{

    public function startup(): void{
        Entity::registerEntity(PigEntity::class, true, ["minecraft:pig"]);
    }
}
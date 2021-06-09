<?php

namespace CLADevs\VanillaX\entities\monster;

class MagmaCubeEntity extends SlimeEntity{

    const NETWORK_ID = self::MAGMA_CUBE;

    public function getName(): string{
        return "Magma Cube";
    }
}
<?php

namespace CLADevs\VanillaX\entities\utils\interfaces;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;

interface EntityInteractable{

    /**
     * when a player interacts with entity such as player trading
     * @param EntityInteractResult $result
     */
    public function onInteract(EntityInteractResult $result): void;
}
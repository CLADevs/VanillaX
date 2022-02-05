<?php

namespace CLADevs\VanillaX\entities\utils;

interface EntityInteractable{

    /**
     * when a player interacts with entity such as player trading
     * @param EntityInteractResult $result
     */
    public function onInteract(EntityInteractResult $result): void;
}
<?php

namespace CLADevs\VanillaX\entities\utils;

interface EntityInteractable{

    public function onInteract(EntityInteractResult $result): void;
}
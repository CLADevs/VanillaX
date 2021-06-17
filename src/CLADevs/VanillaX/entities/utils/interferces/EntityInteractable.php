<?php

namespace CLADevs\VanillaX\entities\utils\interferces;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;

interface EntityInteractable{

    public function onInteract(EntityInteractResult $result): void;
}
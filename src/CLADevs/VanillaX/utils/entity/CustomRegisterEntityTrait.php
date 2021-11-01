<?php

namespace CLADevs\VanillaX\utils\entity;

use Closure;

interface CustomRegisterEntityTrait{

    public static function getRegisterClosure(): Closure;
}
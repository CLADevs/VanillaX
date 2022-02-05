<?php

namespace CLADevs\VanillaX\entities\utils;

use Closure;

interface EntityCustomRegisterClosure{

    public static function getRegisterClosure(): Closure;
}
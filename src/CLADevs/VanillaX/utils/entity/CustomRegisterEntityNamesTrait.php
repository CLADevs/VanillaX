<?php

namespace CLADevs\VanillaX\utils\entity;

interface CustomRegisterEntityNamesTrait{

    /**
     * @return string[]
     */
    public static function getRegisterSaveNames(): array;

    public static function getSaveId(): ?int;
}
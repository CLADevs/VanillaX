<?php

namespace CLADevs\VanillaX\entities\utils;

class EntityIdentifierX{

    private string $mcpeId;
    private string $name;
    private string $namespace;

    private int $id;

    public function __construct(string $mcpeId, string $entityName, string $namespace, int $entityId){
        $this->mcpeId = $mcpeId;
        $this->name = $entityName;
        $this->namespace = $namespace;
        $this->id = $entityId;
    }

    public function getMcpeId(): string{
        return $this->mcpeId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getNamespace(): string{
        return $this->namespace;
    }

    public function getId(): int{
        return $this->id;
    }
}
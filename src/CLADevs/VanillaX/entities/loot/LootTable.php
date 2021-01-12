<?php

namespace CLADevs\VanillaX\entities\loot;

class LootTable{

    /** @var LootPool[] */
    private array $pools = [];

    private array $data;

    public function __construct(array $data){
        $this->data = $data;

        if(isset($data["pools"])){
            foreach($data["pools"] as $pool){
                $this->pools[] = new LootPool($pool);
            }
        }
    }

    /**
     * @return LootPool[]
     */
    public function getPools(): array{
        return $this->pools;
    }
}
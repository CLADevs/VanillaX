<?php

namespace CLADevs\VanillaX\entities\loot;

class LootPool{

    private array $info;

    /** @var LootEntry[] */
    private array $entries = [];
    /** @var int|array */
    private $rolls = 0;

    public function __construct(array $info){
        $this->info = $info;
        $entries = $info["entries"] ?? [];
        $rolls = $info["rolls"] ?? null;

        if(is_int($rolls)){
            $this->rolls = $rolls;
        }elseif(is_array($rolls)){
            $this->rolls = [$rolls["min"] ?? 0, $rolls["max"] ?? 1];
        }
        foreach($entries as $entry){
            $this->entries[] = new LootEntry($entry);
        }
    }

    public function getRoll(): int{
        if(is_array($this->rolls)){
            return mt_rand($this->rolls[0], $this->rolls[1]);
        }
        return $this->rolls;
    }

    /**
     * @return LootEntry[]
     */
    public function getEntries(): array{
        return $this->entries;
    }
}
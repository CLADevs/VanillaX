<?php

namespace CLADevs\VanillaX\network\types\stackrequest;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestAction;

class ItemStackRequestX{

    public function __construct(
        private ItemStackRequest $old,
        private int $filterCause
    ){
    }

    public function getRequestId() : int{ return $this->old->getRequestId(); }

    /** @return ItemStackRequestAction[] */
    public function getActions() : array{ return $this->old->getActions(); }

    /**
     * @return string[]
     * @phpstan-return list<string>
     */
    public function getFilterStrings() : array{ return $this->old->getFilterStrings(); }

    public function getFilterCause(): int{ return $this->filterCause; }

    public static function read(PacketSerializer $in): self{
        $request = ItemStackRequest::read($in);
        return new self($request, $in->getInt());
    }

    public function write(PacketSerializer $out): void{
        $this->old->write($out);
        $out->putInt($this->filterCause);
    }
}
<?php

namespace CLADevs\VanillaX\network\session;

use CLADevs\VanillaX\world\chunk\ChunkCacheX;
use Closure;
use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\UsedChunkStatus;
use pocketmine\utils\Utils;

class NetworkSessionX extends NetworkSession{

    public function startUsingChunk(int $chunkX, int $chunkZ, Closure $onCompletion): void{
        Utils::validateCallableSignature(function() : void{}, $onCompletion);

        $world = $this->getPlayer()->getLocation()->getWorld();
        ChunkCacheX::getInstance($world, $this->getCompressor())->request($chunkX, $chunkZ)->onResolve(

        //this callback may be called synchronously or asynchronously, depending on whether the promise is resolved yet
            function(CompressBatchPromise $promise) use ($world, $onCompletion, $chunkX, $chunkZ) : void{
                if(!$this->isConnected()){
                    return;
                }
                $currentWorld = $this->getPlayer()->getLocation()->getWorld();
                if($world !== $currentWorld or ($status = $this->getPlayer()->getUsedChunkStatus($chunkX, $chunkZ)) === null){
                    $this->getLogger()->debug("Tried to send no-longer-active chunk $chunkX $chunkZ in world " . $world->getFolderName());
                    return;
                }
                if(!$status->equals(UsedChunkStatus::REQUESTED())){
                    //TODO: make this an error
                    //this could be triggered due to the shitty way that chunk resends are handled
                    //right now - not because of the spammy re-requesting, but because the chunk status reverts
                    //to NEEDED if they want to be resent.
                    return;
                }
                $world->timings->syncChunkSend->startTiming();
                try{
                    $this->queueCompressed($promise);
                    $onCompletion();
                }finally{
                    $world->timings->syncChunkSend->stopTiming();
                }
            }
        );
    }
}
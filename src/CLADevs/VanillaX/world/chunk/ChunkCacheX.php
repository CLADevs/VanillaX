<?php

namespace CLADevs\VanillaX\world\chunk;

use CLADevs\VanillaX\scheduler\ChunkRequestTaskX;
use GlobalLogger;
use InvalidArgumentException;
use pocketmine\network\mcpe\cache\ChunkCache;
use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\compression\Compressor;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use ReflectionException;

class ChunkCacheX extends ChunkCache{

    private static array $instances = [];

    private int $hits = 0;
    private int $misses = 0;

    private World $cacheWorld;
    private Compressor $cacheCompressor;

    /** @var CompressBatchPromise[] */
    private array $cachesList = [];

    private function __construct(World $world, Compressor $compressor){
        $this->cacheWorld = $world;
        $this->cacheCompressor = $compressor;
    }

    public static function getInstance(World $world, Compressor $compressor): self{
        $worldId = spl_object_id($world);
        $compressorId = spl_object_id($compressor);
        if(!isset(self::$instances[$worldId])){
            self::$instances[$worldId] = [];
            $world->addOnUnloadCallback(static function() use ($worldId): void{
                foreach(self::$instances[$worldId] as $cache){
                    $cache->cachesList = [];
                }
                unset(self::$instances[$worldId]);
                GlobalLogger::get()->debug("Destroyed chunk packet caches for world#$worldId");
            });
        }
        if(!isset(self::$instances[$worldId][$compressorId])){
            GlobalLogger::get()->debug("Created new chunk packet cache (world#$worldId, compressor#$compressorId)");
            self::$instances[$worldId][$compressorId] = new self($world, $compressor);
        }
        return self::$instances[$worldId][$compressorId];
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @return CompressBatchPromise
     * @throws ReflectionException
     */
    public function request(int $chunkX, int $chunkZ): CompressBatchPromise{
        $this->cacheWorld->registerChunkListener($this, $chunkX, $chunkZ);
        $chunk = $this->cacheWorld->getChunk($chunkX, $chunkZ);
        if($chunk === null){
            throw new InvalidArgumentException("Cannot request an unloaded chunk");
        }
        $chunkHash = World::chunkHash($chunkX, $chunkZ);

        if(isset($this->cachesList[$chunkHash])){
            ++$this->hits;
            return $this->cachesList[$chunkHash];
        }

        ++$this->misses;

        $this->cacheWorld->timings->syncChunkSendPrepare->startTiming();
        try{
            $this->cachesList[$chunkHash] = new CompressBatchPromise();

            $this->cacheWorld->getServer()->getAsyncPool()->submitTask(
                new ChunkRequestTaskX(
                    $chunkX,
                    $chunkZ,
                    $chunk,
                    $this->cachesList[$chunkHash],
                    $this->cacheCompressor,
                    function() use ($chunkX, $chunkZ) : void{
                        $this->cacheWorld->getLogger()->error("Failed preparing chunk $chunkX $chunkZ, retrying");

                        $this->restartPendingRequest($chunkX, $chunkZ);
                    }
                )
            );

            return $this->cachesList[$chunkHash];
        }finally{
            $this->cacheWorld->timings->syncChunkSendPrepare->stopTiming();
        }
    }

    private function restartPendingRequest(int $chunkX, int $chunkZ): void{
        $chunkHash = World::chunkHash($chunkX, $chunkZ);
        $existing = $this->cachesList[$chunkHash] ?? null;
        if($existing === null or $existing->hasResult()){
            throw new InvalidArgumentException("Restart can only be applied to unresolved promises");
        }
        $existing->cancel();
        unset($this->cachesList[$chunkHash]);

        $this->request($chunkX, $chunkZ)->onResolve(...$existing->getResolveCallbacks());
    }

    private function destroy(int $chunkX, int $chunkZ): bool{
        $chunkHash = World::chunkHash($chunkX, $chunkZ);
        $existing = $this->cachesList[$chunkHash] ?? null;
        unset($this->cachesList[$chunkHash]);

        return $existing !== null;
    }

    private function destroyOrRestart(int $chunkX, int $chunkZ): void{
        $cache = $this->cachesList[World::chunkHash($chunkX, $chunkZ)] ?? null;
        if($cache !== null){
            if(!$cache->hasResult()){
                //some requesters are waiting for this chunk, so their request needs to be fulfilled
                $this->restartPendingRequest($chunkX, $chunkZ);
            }else{
                //dump the cache, it'll be regenerated the next time it's requested
                $this->destroy($chunkX, $chunkZ);
            }
        }
    }

    public function onChunkUnloaded(int $chunkX, int $chunkZ, Chunk $chunk): void{
        $this->destroy($chunkX, $chunkZ);
        $this->cacheWorld->unregisterChunkListener($this, $chunkX, $chunkZ);
    }

    public function calculateCacheSize(): int{
        $result = 0;
        foreach($this->cachesList as $cache){
            if($cache->hasResult()){
                $result += strlen($cache->getResult());
            }
        }
        return $result;
    }

    public function getHitPercentage(): float{
        $total = $this->hits + $this->misses;
        return $total > 0 ? $this->hits / $total : 0.0;
    }
}
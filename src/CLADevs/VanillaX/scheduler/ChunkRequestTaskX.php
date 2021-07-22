<?php

namespace CLADevs\VanillaX\scheduler;

use CLADevs\VanillaX\blocks\BlockManager;
use Closure;
use pocketmine\network\mcpe\ChunkRequestTask;
use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\compression\Compressor;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketBatch;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use ReflectionClass;
use ReflectionException;

class ChunkRequestTaskX extends ChunkRequestTask{

    private string $tiles;

    /**
     * ChunkRequestTaskX constructor.
     * @param int $chunkX
     * @param int $chunkZ
     * @param Chunk $chunk
     * @param CompressBatchPromise $promise
     * @param Compressor $compressor
     * @param Closure|null $onError
     * @throws ReflectionException
     */
    public function __construct(int $chunkX, int $chunkZ, Chunk $chunk, CompressBatchPromise $promise, Compressor $compressor, ?Closure $onError = null){
        parent::__construct($chunkX, $chunkZ, $chunk, $promise, $compressor, $onError);
        $tilesProperty = (new ReflectionClass(ChunkRequestTask::class))->getProperty("tiles");
        $tilesProperty->setAccessible(true);
        $this->tiles = $tilesProperty->getValue($this);
    }

    public function onRun(): void{
        BlockManager::getInstance()->initializeRuntimeIds();
        $chunk = FastChunkSerializer::deserialize($this->chunk);
        $subCount = ChunkSerializer::getSubChunkCount($chunk);
        $payload = ChunkSerializer::serialize($chunk, RuntimeBlockMapping::getInstance(), $this->tiles);
        $this->setResult($this->compressor->compress(PacketBatch::fromPackets(LevelChunkPacket::withoutCache($this->chunkX, $this->chunkZ, $subCount, $payload))->getBuffer()));
    }
}
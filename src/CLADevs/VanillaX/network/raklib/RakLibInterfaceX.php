<?php

namespace CLADevs\VanillaX\network\raklib;

use CLADevs\VanillaX\network\session\NetworkSessionX;
use Exception;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\PacketBroadcaster;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\mcpe\raklib\RakLibPacketSender;
use pocketmine\network\mcpe\StandardPacketBroadcaster;
use pocketmine\network\Network;
use pocketmine\network\PacketHandlingException;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use pocketmine\utils\Utils;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\PacketReliability;
use raklib\server\ipc\UserToRakLibThreadMessageSender;
use ReflectionClass;
use ReflectionException;

class RakLibInterfaceX extends RakLibInterface{

    private const MCPE_RAKNET_PACKET_ID = "\xfe";

    private Server $server;
    private Network $network;
    private PacketBroadcaster $broadcaster;
    private UserToRakLibThreadMessageSender $interface;

    /** @var NetworkSessionX[] */
    private array $sessions = [];

    /**
     * RakLibInterfaceX constructor.
     * @param Server $server
     * @throws ReflectionException
     */
    public function __construct(Server $server){
        parent::__construct($server);
        $this->server = $server;
        $this->broadcaster = new StandardPacketBroadcaster($this->server);

        $interfaceProperty = (new ReflectionClass(RakLibInterface::class))->getProperty("interface");
        $interfaceProperty->setAccessible(true);
        $this->interface = $interfaceProperty->getValue($this);
    }

    public function setNetwork(Network $network): void{
        parent::setNetwork($network);
        $this->network = $network;
    }

    public function close(int $sessionId): void{
        if(isset($this->sessions[$sessionId])){
            unset($this->sessions[$sessionId]);
            $this->interface->closeSession($sessionId);
        }
    }

    public function putPacket(int $sessionId, string $payload, bool $immediate = true): void{
        if(isset($this->sessions[$sessionId])){
            $pk = new EncapsulatedPacket();
            $pk->buffer = self::MCPE_RAKNET_PACKET_ID . $payload;
            $pk->reliability = PacketReliability::RELIABLE_ORDERED;
            $pk->orderChannel = 0;

            $this->interface->sendEncapsulated($sessionId, $pk, $immediate);
        }
    }

    public function onClientConnect(int $sessionId, string $address, int $port, int $clientID): void{
        $session = new NetworkSessionX(
            $this->server,
            $this->network->getSessionManager(),
            PacketPool::getInstance(),
            new RakLibPacketSender($sessionId, $this),
            $this->broadcaster,
            ZlibCompressor::getInstance(),
            $address,
            $port
        );
        $this->sessions[$sessionId] = $session;
    }

    public function onClientDisconnect(int $sessionId, string $reason): void{
        if(isset($this->sessions[$sessionId])){
            $session = $this->sessions[$sessionId];
            unset($this->sessions[$sessionId]);
            $session->onClientDisconnect($reason);
        }
    }

    /**
     * @param int $sessionId
     * @param string $packet
     * @throws Exception
     */
    public function onPacketReceive(int $sessionId, string $packet): void{
        if(isset($this->sessions[$sessionId])){
            if($packet === "" or $packet[0] !== self::MCPE_RAKNET_PACKET_ID){
                $this->sessions[$sessionId]->getLogger()->debug("Non-FE packet received: " . base64_encode($packet));
                return;
            }
            //get this now for blocking in case the player was closed before the exception was raised
            $session = $this->sessions[$sessionId];
            $address = $session->getIp();
            $buf = substr($packet, 1);
            try{
                $session->handleEncoded($buf);
            }catch(PacketHandlingException $e){
                $errorId = bin2hex(random_bytes(6));

                $logger = $session->getLogger();
                $logger->error("Bad packet (error ID $errorId): " . $e->getMessage());

                //intentionally doesn't use logException, we don't want spammy packet error traces to appear in release mode
                $logger->debug("Origin: " . Filesystem::cleanPath($e->getFile()) . "(" . $e->getLine() . ")");
                foreach(Utils::printableTrace($e->getTrace()) as $frame){
                    $logger->debug($frame);
                }
                $session->disconnect("Packet processing error (Error ID: $errorId)");
                $this->interface->blockAddress($address, 5);
            }
        }
    }

    public function onPingMeasure(int $sessionId, int $pingMS): void{
        if(isset($this->sessions[$sessionId])){
            $this->sessions[$sessionId]->updatePing($pingMS);
        }
    }
}
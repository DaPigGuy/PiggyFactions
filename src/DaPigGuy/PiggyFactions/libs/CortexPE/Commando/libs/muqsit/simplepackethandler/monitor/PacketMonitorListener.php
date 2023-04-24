<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\CortexPE\Commando\libs\muqsit\simplepackethandler\monitor;

use Closure;
use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\libs\muqsit\simplepackethandler\utils\ClosureSignatureParser;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class PacketMonitorListener implements IPacketMonitor, Listener{

	private ?Closure $incoming_event_handler = null;
	private ?Closure $outgoing_event_handler = null;

	/**
	 * @var Closure[][]
	 * @phpstan-var array<int, array<Closure(ServerboundPacket, NetworkSession) : void>>
	 */
	private array $incoming_handlers = [];

	/**
	 * @var Closure[][]
	 * @phpstan-var array<int, array<Closure(ClientboundPacket, NetworkSession) : void>>
	 */
	private array $outgoing_handlers = [];

	public function __construct(
		private Plugin $register,
		private bool $handleCancelled
	){}

	public function monitorIncoming(Closure $handler) : IPacketMonitor{
		$classes = ClosureSignatureParser::parse($handler, [ServerboundPacket::class, NetworkSession::class], "void");
		assert(is_a($classes[0], DataPacket::class, true));
		$this->incoming_handlers[$classes[0]::NETWORK_ID][spl_object_id($handler)] = $handler;

		if($this->incoming_event_handler === null){
			Server::getInstance()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, $this->incoming_event_handler = function(DataPacketReceiveEvent $event) : void{
				/** @var DataPacket&ServerboundPacket $packet */
				$packet = $event->getPacket();
				if(isset($this->incoming_handlers[$pid = $packet::NETWORK_ID])){
					$origin = $event->getOrigin();
					foreach($this->incoming_handlers[$pid] as $handler){
						$handler($packet, $origin);
					}
				}
			}, EventPriority::MONITOR, $this->register, $this->handleCancelled);
		}

		return $this;
	}

	public function monitorOutgoing(Closure $handler) : IPacketMonitor{
		$classes = ClosureSignatureParser::parse($handler, [ClientboundPacket::class, NetworkSession::class], "void");
		assert(is_a($classes[0], DataPacket::class, true));
		$this->outgoing_handlers[$classes[0]::NETWORK_ID][spl_object_id($handler)] = $handler;

		if($this->outgoing_event_handler === null){
			Server::getInstance()->getPluginManager()->registerEvent(DataPacketSendEvent::class, $this->outgoing_event_handler = function(DataPacketSendEvent $event) : void{
				/** @var DataPacket&ClientboundPacket $packet */
				foreach($event->getPackets() as $packet){
					if(isset($this->outgoing_handlers[$pid = $packet::NETWORK_ID])){
						foreach($event->getTargets() as $target){
							foreach($this->outgoing_handlers[$pid] as $handler){
								$handler($packet, $target);
							}
						}
					}
				}
			}, EventPriority::MONITOR, $this->register, $this->handleCancelled);
		}

		return $this;
	}
}
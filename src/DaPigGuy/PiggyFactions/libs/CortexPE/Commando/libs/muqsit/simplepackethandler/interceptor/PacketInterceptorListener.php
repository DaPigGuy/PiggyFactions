<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\CortexPE\Commando\libs\muqsit\simplepackethandler\interceptor;

use Closure;
use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\libs\muqsit\simplepackethandler\utils\ClosureSignatureParser;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class PacketInterceptorListener implements IPacketInterceptor, Listener{

	private ?Closure $incoming_event_handler = null;
	private ?Closure $outgoing_event_handler = null;

	/**
	 * @var Closure[][]
	 * @phpstan-var array<int, array<Closure(ServerboundPacket, NetworkSession) : bool>>
	 */
	private array $incoming_handlers = [];

	/**
	 * @var Closure[][]
	 * @phpstan-var array<int, array<Closure(ClientboundPacket, NetworkSession) : bool>>
	 */
	private array $outgoing_handlers = [];

	public function __construct(
		private Plugin $register,
		private int $priority,
		private bool $handleCancelled
	){}

	public function interceptIncoming(Closure $handler) : IPacketInterceptor{
		$classes = ClosureSignatureParser::parse($handler, [ServerboundPacket::class, NetworkSession::class], "bool");
		assert(is_a($classes[0], DataPacket::class, true));
		$this->incoming_handlers[$classes[0]::NETWORK_ID][spl_object_id($handler)] = $handler;

		if($this->incoming_event_handler === null){
			Server::getInstance()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, $this->incoming_event_handler = function(DataPacketReceiveEvent $event) : void{
				/** @var DataPacket&ServerboundPacket $packet */
				$packet = $event->getPacket();
				if(isset($this->incoming_handlers[$pid = $packet::NETWORK_ID])){
					$origin = $event->getOrigin();
					foreach($this->incoming_handlers[$pid] as $handler){
						if(!$handler($packet, $origin)){
							$event->cancel();
							break;
						}
					}
				}
			}, $this->priority, $this->register, $this->handleCancelled);
		}

		return $this;
	}

	public function interceptOutgoing(Closure $handler) : IPacketInterceptor{
		$classes = ClosureSignatureParser::parse($handler, [ClientboundPacket::class, NetworkSession::class], "bool");
		assert(is_a($classes[0], DataPacket::class, true));
		$this->outgoing_handlers[$classes[0]::NETWORK_ID][spl_object_id($handler)] = $handler;

		if($this->outgoing_event_handler === null){
			Server::getInstance()->getPluginManager()->registerEvent(DataPacketSendEvent::class, $this->outgoing_event_handler = function(DataPacketSendEvent $event) : void{
				$original_targets = $event->getTargets();
				$packets = $event->getPackets();

				/** @var DataPacket&ClientboundPacket $packet */
				foreach($packets as $packet){
					if(isset($this->outgoing_handlers[$pid = $packet::NETWORK_ID])){
						$remaining_targets = $original_targets;

						foreach($remaining_targets as $i => $target){
							foreach($this->outgoing_handlers[$pid] as $handler){
								if(!$handler($packet, $target)){
									unset($remaining_targets[$i]);
									break;
								}
							}
						}

						$remaining_targets_c = count($remaining_targets);
						if($remaining_targets_c !== count($original_targets)){
							$event->cancel();
							if($remaining_targets_c > 0){
								$new_target_players = [];
								foreach($remaining_targets as $new_target){
									$new_target_player = $new_target->getPlayer();
									if($new_target_player !== null){
										$new_target_players[] = $new_target_player;
									}
								}

								Server::getInstance()->broadcastPackets($new_target_players, $packets);
							}
							break;
						}
					}
				}
			}, $this->priority, $this->register, $this->handleCancelled);
		}

		return $this;
	}
}
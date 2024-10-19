<?php

declare(strict_types=1);

namespace yuki_matsuura\transit;

use yuki_matsuura\transit\config\Config;
use yuki_matsuura\transit\utils\SingletonTrait;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\SecureServer;
use React\Socket\Server;

class ServerManager {
    use SingletonTrait;

    public const int DEFAULT_PORT = 8291;

    private ?IoServer $server;
    private readonly int $port;

    public function __construct() {
        $this->server = null;
        /** @var string|int $port */
        $port = getopt("p:")["p"] ?? self::DEFAULT_PORT;
        $this->port = (int) $port;
        $this->setInstance();
    }

    public function makeServer():self {
        $httpServer = new HttpServer(
            new WsServer(
                new MessageManager()
            )
        );
        if (Config::getInstance()->get("server", "development") === "production") {
            $loop = Factory::create();
            $this->server = new IoServer(
                $httpServer,
                new SecureServer(
                    new Server("0.0.0.0:". $this->port, $loop),
                    $loop,
                    [
                        "local_cert" => "/etc/letsencrypt/live/your_domain/cert.pem",
                        "local_pk" => "/etc/letsencrypt/live/vc-ymgs.f5.si/privkey.pem",
                        "verify_peer" => false
                    ]
                ),
                $loop
            );
        } else {
            $this->server = IoServer::factory($httpServer, $this->port);
        }
        return $this;
    }

    public function addPeriodicTimer(Task $task, int|float $interval):self {
        if ($this->server === null) {
            return $this;
        }
        $this->server->loop->addPeriodicTimer($interval, function() use ($task) {
            $task->onRun();
        });
        return $this;
    }

    public function getServer():?IoServer {
        return $this->server ?? null;
    }

    public function getPort():int {
        return $this->port;
    }

    public function runServer():never {
        if ($this->server === null) {
            throw new \LogicException("IoServer is not set");
        }
        echo "Server is now running in localhost:". $this->getPort();
        $this->server->run();
        $this->onEnd();
    }

    private function onEnd():never {
        exit();
    }

}
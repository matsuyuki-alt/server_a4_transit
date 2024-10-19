<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\model;

use Ratchet\ConnectionInterface;

readonly class Observer {
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function getConnection():ConnectionInterface {
        return $this->connection;
    }

    private function sendMessage(string $message):self {
        $this->connection->send($message);
        return $this;
    }

    public function sendData(array $data):self {
        $this->sendMessage(json_encode($data));
        return $this;
    }

    public function onReceiveData(array $data):void {

    }

}
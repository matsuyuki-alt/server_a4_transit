<?php

declare(strict_types=1);

namespace yuki_matsuura\transit;

use yuki_matsuura\transit\cache\ObserverList;
use yuki_matsuura\transit\model\Observer;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class MessageManager implements MessageComponentInterface {
    /** @inheritDoc */
    function onOpen(ConnectionInterface $conn):void {
        $observerList = ObserverList::getInstance();
        if ($observerList->checkContainsByConnection($conn)) {
            return;
        }
        $observerList->attach(new Observer($conn));
    }

    /** @inheritDoc */
    function onClose(ConnectionInterface $conn):void {
        $observerList = ObserverList::getInstance();
        $observer = $observerList->getObserverByConnection($conn);
        if ($observer === null) {
            return;
        }
        $observerList->detach($observer);
    }

    /** @inheritDoc */
    function onError(ConnectionInterface $conn, \Exception $e):void {
        $conn->close();
    }

    /** @inheritDoc */
    function onMessage(ConnectionInterface $from, $msg):void {
        if (!json_validate($msg)) {
            return;
        }
        ObserverList::getInstance()->getObserverByConnection($from)->onReceiveData(json_decode($msg, true));
    }

}
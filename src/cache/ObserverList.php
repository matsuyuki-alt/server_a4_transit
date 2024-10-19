<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\cache;

use yuki_matsuura\transit\model\Observer;
use yuki_matsuura\transit\utils\SingletonTrait;
use Ratchet\ConnectionInterface;

class ObserverList {
    use SingletonTrait;

    /** @var Observer[] */
    private array $observers;

    public function __construct() {
        $this->observers = [];
        $this->setInstance();
    }

    public function attach(Observer $observer):void {
        $this->observers[] = $observer;
    }

    /** Removes the specified Observer from the list. An unlisted observer will be ignored silently. */
    public function detach(Observer $observer):void {
        foreach ($this->observers as $index => $compare) {
            if ($compare === $observer) {
                unset($this->observers[$index]);
                $this->observers = array_values($this->observers);
                break;
            }
        }
    }

    public function contains(Observer $observer):bool {
        return in_array($observer, $this->observers, true);
    }

    public function getObserverByConnection(ConnectionInterface $connection):?Observer {
        foreach ($this->observers as $observer) {
            if ($observer->getConnection() === $connection) {
                return $observer;
            }
        }
        return null;
    }

    public function checkContainsByConnection(ConnectionInterface $connection):bool {
        return $this->getObserverByConnection($connection) !== null;
    }

    /** @return Observer[] */
    public function getAll():array {
        return $this->observers;
    }

}
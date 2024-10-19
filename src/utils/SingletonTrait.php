<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\utils;

trait SingletonTrait {
    private static ?self $instance = null;

    private function setInstance():void {
        self::$instance = $this;
    }

    public static function getInstance():?self {
        return self::$instance;
    }

    public static function haveInstance():bool {
        return self::$instance !== null;
    }
}
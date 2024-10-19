<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\config;

use yuki_matsuura\transit\utils\SingletonTrait;
use Symfony\Component\Yaml\Yaml;

class Config {
    use SingletonTrait;

    private array $data;

    public function __construct() {
        $this->load();
        $this->setInstance();
    }

    public function load():void {
        $this->data = Yaml::parseFile(realpath(__DIR__. "/../../resources/config.yml"));
    }

    public function getAll():array {
        return $this->data;
    }

    public function get(string $key, mixed $default = false):mixed {
        return $this->data[$key] ?? $default;
    }

}
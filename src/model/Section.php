<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\model;

class Section {
    private readonly string $start;
    private readonly string $end;
    private readonly int $timeRequiredSec;
    private int|float $delayPercent /* 混雑などによる所要時間の増減を示す比率。デフォルトは1で、例えば2なら所要時間が2倍 */;
    private array $buses;

    public function __construct(string $start, string $end, int $timeRequiredSec) {
        $this->start = $start;
        $this->end = $end;
        $this->timeRequiredSec = $timeRequiredSec;
        $this->delayPercent = 1.0;
    }

    public function getStart():string {
        return $this->start;
    }

    public function getEnd():string {
        return $this->end;
    }

    public function getTimeRequiredSec():int {
        return $this->timeRequiredSec;
    }

    public function getBuses():array {
        return $this->buses;
    }

    public function getDelayPercent():int|float {
        return $this->delayPercent;
    }

    public function calculateActualTimeRequiredSec():int {
        return (int) round($this->timeRequiredSec * $this->delayPercent);
    }

    public function setBuses(array $buses):self {
        $this->buses = $buses;
        return $this;
    }

    public function setDelayPercent(int|float $delayPercent = 1.0):self {
        $this->delayPercent = $delayPercent;
        return $this;
    }

}
<?php

declare(strict_types=1);

namespace yuki_matsuura\transit\model;

readonly class Route {
    /* 阪神御影が始点。六甲ケーブル下/鶴甲団地が終点 */
    public const string DESTINATION = "north";

    private int $systemNumber /* 系統番号 */;
    /** @var Section[] */
    private array $sections;

    public function __construct(int $systemNumber, array $sections) {
        $this->systemNumber = $systemNumber;
        $this->sections = $sections;
    }

    public function getSystemNumber():int {
        return $this->systemNumber;
    }

    public function getSections():array {
        return $this->sections;
    }

    public function getSectionByBusStop(string $start, string $end):?Section {
        foreach ($this->sections as $section) {
            if ($section->getStart() === $start and $section->getEnd() === $end) {
                return $section;
            }
        }
        return null;
    }

    /** @return null|Section[] */
    public function getSectionsByBusStop(string $start, string $end):?array {
        $startDefined = $endDefined = false;
        $return = [];
        foreach ($this->sections as $section) {
            if ($start === $section->getStart()) {
                $startDefined = true;
            }
            if ($startDefined and !$endDefined) {
                $return[] = $section;
            }
            if ($end === $section->getEnd()) {
                $endDefined = true;
            }
        }
        return ($startDefined and $endDefined) ? $return : null;
    }

    public function getTimeRequiredSec(string $start, string $end, bool $actual = true):?int {
        $sections = $this->getSectionsByBusStop($start, $end);
        if ($sections === null) {
            return null;
        }
        if ($actual) {
            return array_sum(array_map(fn(Section $section) => $section->calculateActualTimeRequiredSec(), $sections));
        }
        return array_sum(array_map(fn(Section $section) => $section->getTimeRequiredSec(), $sections));
    }

    /** @return string[] */
    public function getTerminal():array {
        return [
            $this->sections[0]->getStart(),
            $this->sections[count($this->sections) - 1]->getEnd()
        ];
    }

}
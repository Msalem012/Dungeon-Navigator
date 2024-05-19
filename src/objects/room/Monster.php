<?php

namespace task1\objects\room;

use Exception;

class Monster
{
    /**
     * @var int
     */
    private $level;

    /** Сила монстра
     * @var int
     */
    private $power;

    public function __construct($level)
    {
        $this->level = $level;
        $this->power = null;
    }

    public function getMonsterLevel() {
        return $this->level;
    }

    /**
     * Сила монстра
     * @return int
     */
    public function getPower():int {
        if (is_null($this->power)) {
            $points = MONSTER_LEVELS[$this->level];
            try {
                $this->power = random_int($points[0], $points[1]);
            } catch (Exception $e) {
                $this->power = 0;
            }
        }

        return $this->power;
    }

    /** Модификатор урона
     * @return float
     */
    public function getMod():float {
        $points = MONSTER_LEVELS[$this->level];
        return (float)$points[2];
    }
}
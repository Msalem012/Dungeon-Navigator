<?php

namespace task1\objects\room;

use Exception;

/**
 * Сундук
 */
class Treasure
{
    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $points;

    public function __construct(int $level)
    {
        $this->level = $level;
        $this->points = null;
    }

    /** Уровень сундука
     * @return int
     */
    public function getLevel():int {
        return $this->level;
    }

    /** Количество очков в сундуке
     * @return int
     */
    public function getPoints():int {
        if (is_null($this->points)) {
            $points = TREASURES_LEVELS[$this->level];
            try {
                $this->points = random_int($points[0], $points[1]);
            } catch (Exception $e) {
                $this->points = 0;
            }
        }

        return $this->points;
    }
}
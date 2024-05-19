<?php

namespace task1\objects\player;

/**
 * Персонаж
 */
class Player
{
    /**
     * Количество очков персонажа
     * @var int
     */
    private $score;

    /**
     * @param int $score начальное количество очков
     */
    public function __construct(int $score = 0)
    {
        $this->score = $score;
    }

    /**
     * Возвращает количество очков персонажа
     * @return int
     */
    public function getScore():int {
        return $this->score;
    }

    /**
     * Устанавливает новое количетсво очков персонажа <br>
     * Очки не могут быть отрицательные
     * @param int $newScore
     * @return void
     */
    public function setScore(int $newScore) {
        $this->score = max($newScore, 0);
    }

    /**
     * Добавляет количество очков
     * @param int $score
     * @return int итоговое количество очков
     */
    public function addScore(int $score):int {
        $this->setScore($this->score + $score);
        return $this->getScore();
    }

    /**
     * Уменьшает количество очков
     * @param int $score
     * @return int итоговое количество очков
     */
    public function reduceScore(int $score):int {
        $this->setScore($this->score - $score);
        return $this->getScore();
    }
}
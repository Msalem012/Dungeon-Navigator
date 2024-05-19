<?php

namespace task1\objects\room;

require __DIR__ . DS . 'Treasure.php';
require __DIR__ . DS . 'Monster.php';

/**
 * Комната
 */
class Room
{
    /**
     * @var int
     */
    private $id;

    /** Двери в другие комнаты
     * @var int[]
     */
    private $doors;

    /**
     * @var Treasure|null
     */
    private $treasure;

    /**
     * @var Monster|null
     */
    private $monster;

    /**
     * @param int $id
     * @param array $doors
     */
    public function __construct(int $id, array $doors = [], $treasure = null, $monster = null)
    {
        $this->id = $id;
        $this->doors = $doors;
        $this->treasure = $treasure;
        $this->monster = $monster;
    }

    /** Уникальный индекс комнаты
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getDoors():array {
        return $this->doors;
    }

    /**
     * Получить двери для вывода в консоль
     * @return array
     */
    public function getDoorsPrint():array {
        $doors = $this->doors;
        $doorsPrint = [];

        foreach ($doors as $door) {
            if ($door == PLAYER_END_POS) {
                $doorsPrint[] = 'Выход';
            } else if ($door != PLAYER_START_POS) {
                $doorsPrint[] = $door;
            }
        }

        return $doorsPrint;
    }

    /** Есть ли в комнате сундук
     * @return bool
     */
    public function existsTreasure():bool {
        return !is_null($this->treasure);
    }

    /** Открыть сундук
     * @return int
     */
    public function getTreasureLevel():int {
        if ($this->existsTreasure()) {
            return $this->treasure->getLevel();
        }

        return 0;
    }

    /** Открыть сундук
     * @return int
     */
    public function openTreasure():int {
        if ($this->existsTreasure()) {
            $points = $this->treasure->getPoints();
            $this->treasure = null;
            return $points;
        }

        return 0;
    }

    /** Есть ли в комнате монстр
     * @return bool
     */
    public function existsMonster():bool {
        return !is_null($this->monster);
    }

    public function getMonster() {
        return $this->monster;
    }

    public function killMonster() {
        $this->monster = null;
    }
}
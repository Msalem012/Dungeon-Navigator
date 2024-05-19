<?php

namespace task1\objects\dungeon;

use Exception;
use task1\objects\player\Player;
use task1\objects\room\Room;

require_once dirname(__DIR__) . DS . 'room' . DS . "Room.php";
require_once dirname(__DIR__) . DS . 'player' . DS . 'Player.php';

/**
 * Подземелье
 */
class Dungeon
{
    /**
     * @var Room[]
     */
    private $rooms;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var int
     */
    private $playerPosition;

    /** Кратчайший путь
     * @var string|null
     */
    private $easyWay;

    public function __construct($rooms = [], $playerScore = 0, $easyWay = null)
    {
        $this->rooms = $rooms;
        $this->player = new Player($playerScore);
        $this->playerPosition = PLAYER_START_POS;
        $this->easyWay = $easyWay;
        $this->getEasyWay();
    }

    /**
     * @param int $id
     * @return Room|null
     */
    private function getRoomByRoomId(int $id): ?Room
    {
        foreach ($this->rooms as $room) {
            if ($room->getId() == $id) {
                return $room;
            }
        }

        return null;
    }

    private function getLastRoomId(): int
    {
        $id = 1;
        foreach ($this->rooms as $room) {
            if ($room->getId() > $id) {
                $id = $room->getId();
            }
        }

        return $id;
    }

    /** Получить очки игрока
     * @return int
     */
    public function getPlayerScore(): int
    {
        return $this->player->getScore();
    }

    /**
     * @return int
     */
    public function getPlayerPosition(): int
    {
        return $this->playerPosition;
    }

    public function getPlayerDoors($print = false): array
    {
        if ($this->playerPosition == PLAYER_START_POS) {
            return [1];
        } else if ($this->playerPosition == PLAYER_END_POS) {
            return [];
        } else {
            $id = $this->playerPosition;
            $room = $this->getRoomByRoomId($id);
            return $print ? $room->getDoorsPrint() : $room->getDoors();
        }
    }

    /** Может ли игрок пойти в команту
     * @param $door
     * @return bool
     */
    private function playerCanGoTo($door): bool
    {
        $doors = $this->getPlayerDoors();
        return in_array($door, $doors);
    }

    private function fight()
    {
        $room = $this->getRoomByRoomId($this->playerPosition);
        $monsterPowerMax = $room->getMonster()->getPower();
        echo "В комнате есть монстр | уровень монстра {$room->getMonster()->getMonsterLevel()} | Сила монстра $monsterPowerMax | начинется битва" . ENDL;

        $monsterPower = $room->getMonster()->getPower();
        $monsterMod = $room->getMonster()->getMod();
        $round = 0;
        do {
            $round++;
            echo "Раунд $round | Сила монстра $monsterPower | ";
            try {
                $playerPower = random_int(MIN_PLAYER_POWER, MAX_PLAYER_POWER);
                echo "Сила игрока $playerPower | ";
                if ($playerPower > $monsterPower) {
                    echo "Игрок победил и получает очки: $monsterPowerMax" . ENDL;
                    $this->player->addScore($monsterPowerMax);
                    $monsterPower = 0;
                } else {
                    $monsterDamage = (int)($playerPower * $monsterMod);
                    $monsterPower -= $monsterDamage;
                    echo "Монстр получает урон $monsterDamage | Текущая сила монстра: $monsterPower" . ENDL;
                }
            } catch (Exception $e) {
                break;
            }
        } while ($monsterPower > 0);

        $room->killMonster();
    }

    /** Пойти в комнату
     * @param $door
     * @return bool
     */
    public function playerGo($door): bool
    {
        if (!$this->playerCanGoTo($door)) {
            echo "Игрок не может пойти в выбранную комнату: $door" . ENDL;
            return false;
        }

        $this->playerPosition = $door;
        echo "Игрок перешел в выбранную комнтау: $door" . ENDL;

        $room = $this->getRoomByRoomId($this->playerPosition);
        if ($room->existsTreasure()) {
            echo "В комнате есть сундук | уровень сундука {$room->getTreasureLevel()}" . ENDL;

            $points = $room->openTreasure();
            $this->player->addScore($points);
            echo "Игрок открывает сундук и получает $points очков | очки игрока: {$this->player->getScore()}" . ENDL;
        }

        if ($room->existsMonster()) {
            $this->fight();
        }

        return true;
    }

    public function playerFinish(): bool
    {
        if ($this->playerPosition == PLAYER_END_POS) {
            echo "Игрок уже вышел из лабиринта" . ENDL;
            return false;
        }

        if (!$this->playerCanGoTo(PLAYER_END_POS)) {
            echo "Игрок не может выйти из лабиринта" . ENDL;
            return false;
        }

        $this->playerPosition = PLAYER_END_POS;
        echo "Игрок вышел из лабиринта" . ENDL;
        return true;
    }

    public function getEasyWay(): string
    {
        if (is_null($this->easyWay)) {
            $ways = $this->startCalcAllWays();

            $this->easyWay = $ways[0];
            $count = count($this->easyWay);
            foreach ($ways as $way) {
                $count2 = count($way);
                if ($count2 < $count) {
                    $this->easyWay = $way;
                    $count = $count2;
                }
            }

            $this->easyWay = implode('-', $this->easyWay);
        }

        return $this->easyWay;
    }

    private function startCalcAllWays(): array
    {
        $start = 1;
        $way = [1];

        $waysNext = $this->getNextWays($way, $start);
        $ways = $this->calcAllWays($waysNext);
        $ways2 = [];
        foreach ($ways as $way) {
            $last = $way[count($way) - 1];
            if ($last == $this->getLastRoomId()) {
                $ways2[] = $way;
            }
        }
        return $ways2;
    }

    private function calcAllWays($ways)
    {
        $waysNew = [];
        $lastRoomId = $this->getLastRoomId();

        foreach ($ways as $way) {
            $lastRoom = $way[count($way) - 1];

            $ways2 = [];
            if ($lastRoomId == $lastRoom) {
                return $ways;
            } else {
                $ways2 = $this->getNextWays($way, $lastRoom);
            }

            foreach ($ways2 as $way2) {
                if (empty($way2)) {
                    continue;
                }

                $way2_copy = [];
                foreach ($way2 as $door2) {
                    $way2_copy[] = $door2;
                }
                $waysNew[] = $way2_copy;
            }
        }

        return $this->calcAllWays($waysNew);
    }

    /**
     * @param array $way
     * @param int $roomId
     * @return array
     */
    private function getNextWays($way, $roomId)
    {
        $room = $this->getRoomByRoomId($roomId);
        $doors = $room->getDoors();

        $ways = [];

        foreach ($doors as $door) {
            $wayNext = array_merge(array(), $way);
            if ($door > $roomId) {
                $wayNext[] = $door;
            } else {
                continue;
            }

            $ways[] = $wayNext;
        }

        return $ways;
    }
}
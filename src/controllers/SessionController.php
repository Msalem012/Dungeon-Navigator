<?php

namespace task\controllers;

use task1\objects\dungeon\Dungeon;

require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'dungeon' . DS . 'Dungeon.php';

/**
 * Контроллер игровой сессии
 */
class SessionController
{
    /**
     * @var SessionController|null
     */
    private static $sessionController = null;

    /**
     * @return SessionController
     */
    public static function getInstance(): SessionController
    {
        if (is_null(self::$sessionController)) {
            self::$sessionController = new SessionController();
        }

        return self::$sessionController;
    }

    /**
     * @var Dungeon|null
     */
    private $dungeon;

    public function __construct()
    {
        $this->dungeon = null;
    }

    /**
     * @return bool
     */
    private function sessionExists(): bool
    {
        return file_exists(SESSION_FILE);
    }

    private function sessionSave(): bool
    {
        if (DEBUG) echo "session saving" . ENDL;
        $objData = serialize($this->dungeon);
        if (DEBUG) {
            echo "serialized data: ";
            var_dump($objData);
        }

        $sessionFile = SESSION_FILE;
        if (DEBUG) echo "sessionFile: $sessionFile" . ENDL;

        $fp = fopen($sessionFile, "w");
        if ($fp) {
            fwrite($fp, $objData);
            fclose($fp);
            return true;
        }

        return false;
    }

    private function sessionLoad(): bool
    {
        if ($this->sessionExists()) {
            if (DEBUG) echo "session loading" . ENDL;
            $sessionFile = SESSION_FILE;
            $objData = file_get_contents($sessionFile);
            if (DEBUG) {
                echo "serialized data: ";
                var_dump($objData);
            }

            $dungeon = unserialize($objData);
            if (DEBUG) {
                echo "serialized dungeon: ";
                var_dump($dungeon);
            }

            $this->dungeon = $dungeon;
            return true;
        }

        return false;
    }

    /**
     * Напечтать статус игровой сессии
     * @return void
     */
    public function printSessionStatus()
    {
        if (!$this->sessionExists()) {
            echo "Игровая сессия не активна" . ENDL;
        }

        if ($this->sessionLoad()) {
            echo GAME_DELIMITER . ENDL;

            // Выводим очки игрока
            echo "Очки Игрока: {$this->dungeon->getPlayerScore()}" . ENDL;

            // Выводим положение игрока
            if ($this->dungeon->getPlayerPosition() === PLAYER_START_POS) {
                echo "Игрок в начале подземелья. Начало игры" . ENDL;
            } else if ($this->dungeon->getPlayerPosition() === PLAYER_END_POS) {
                echo "Игрок вышел из подземелья. Игра уже окончена" . ENDL;
            } else {
                echo "Текущая комната: {$this->dungeon->getPlayerPosition()}" . ENDL;
            }

            echo GAME_DELIMITER . ENDL;
            $doors = $this->dungeon->getPlayerDoors(true);
            echo "Игрок может пойти в комнаты: [ " . implode(" | ", $doors) . " ]";
            echo ENDL;
        }
    }

    /**
     * Старт новой сесси
     * @param Dungeon $dungeon
     * @return void
     */
    public function startSession(Dungeon $dungeon)
    {
        echo "Создание новой игровой сессии" . ENDL;
        $this->dungeon = $dungeon;
        if ($this->sessionSave()) echo "Игровая сессия создана" . ENDL;

        $this->printSessionStatus();
    }

    /**
     * @param int $door дверь в комнату
     * @return void
     */
    public function go(int $door)
    {
        $this->sessionLoad();
        $this->dungeon->playerGo($door);
        $this->sessionSave();

        $this->printSessionStatus();
    }

    /** Завершить путешесвтие в подземелье
     * @return void
     */
    public function finish() {
        $this->sessionLoad();
        $this->dungeon->playerFinish();
        $this->sessionSave();

        echo GAME_DELIMITER . ENDL;
        echo "Финальные очки игрока: {$this->dungeon->getPlayerScore()}" . ENDL;
        echo "Кратчайший путь в подземелье: {$this->dungeon->getEasyWay()}" . ENDL;

        $resultFile = RESULT_DIR . DS . date('Y_m_d_H_i_s', time()) . '.txt';
        file_put_contents($resultFile, $this->dungeon->getPlayerScore());
    }
}
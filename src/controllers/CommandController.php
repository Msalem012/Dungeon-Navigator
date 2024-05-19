<?php

namespace task\controllers;

require_once __DIR__ . DS . 'SessionController.php';
require_once __DIR__ . DS . 'LoadController.php';

class CommandController
{
    public const COMMAND_STATUS = 'info';
    public const COMMAND_START = 'start';
    public const COMMAND_GO = 'go';
    public const COMMAND_FINISH= 'finish';

    /**
     * @var CommandController|null
     */
    private static $commandController = null;

    /**
     * @return CommandController
     */
    public static function getInstance(): CommandController
    {
        if (is_null(self::$commandController)) {
            self::$commandController = new CommandController();
        }

        return self::$commandController;
    }

    public function __construct()
    {
    }

    /** Обработчик команд
     * @param $argv
     * @return void
     */
    public function processCommand($argv)
    {
        if (DEBUG) {
            echo "argv: ";
            var_dump($argv);
        }

        if (count($argv) < 2) {
            echo GAME_DELIMITER . ENDL;
            echo "Комманда не введена, введите команду." . ENDL;
            echo "Доступные команды:" . ENDL;
            echo "info - получить текущий статус игры" . ENDL;
            echo "start <number|path> - старт игры и загрузка лабиринта" . ENDL;
            echo 'go <number> - переход в выбранную комнату' . ENDL;
            echo "finish - завершение игры" . ENDL;
            echo GAME_DELIMITER . ENDL;
            echo "Подробнее смотрите в README.md" . ENDL;
            echo GAME_DELIMITER . ENDL;
            return;
        }

        $command = $this->getCommandFromArguments($argv);

        switch ($command) {
            case self::COMMAND_STATUS:
                SessionController::getInstance()->printSessionStatus();
                break;

            case self::COMMAND_START:

                $dungeonPath = $this->getArgByIndex($argv, 2);
                if (is_null($dungeonPath)) {
                    echo "Вы не ввели путь до json файла лабиринта или номер лабиринта из папки dungeons" . ENDL;
                    return;
                }

                var_dump(strpos("\\", $dungeonPath));
                if (strpos($dungeonPath, "http://") !== false || strpos($dungeonPath, "https://") !== false) {
                    $dungeon = LoadController::getInstance()->loadByUrl($dungeonPath);
                } else if (strpos($dungeonPath, "/") !== false || strpos($dungeonPath, "\\") !== false) {
                    $dungeon = LoadController::getInstance()->loadByUrl($dungeonPath);
                } else {
                    $dungeonIndex = (int)$dungeonPath;
                    $dungeon = LoadController::getInstance()->loadByIndex($dungeonIndex);
                }

                SessionController::getInstance()->startSession($dungeon);
                break;

            case self::COMMAND_GO:
                $door = $this->getArgByIndex($argv, 2);
                if (is_null($door)) {
                    echo "Вы не ввели номер комнаты" . ENDL;
                    return;
                }

                SessionController::getInstance()->go($door);
                break;

            case self::COMMAND_FINISH:
                SessionController::getInstance()->finish();
                break;
        }
    }

    private function getArgByIndex($argv, $index): ?string {
        return count($argv) > $index ? $argv[$index] : null;
    }

        /** Получить команду из аргументов коммандной строки
     * @param $argv
     * @return string
     */
    private function getCommandFromArguments($argv): string
    {
        return $this->getArgByIndex($argv, 1);
    }
}
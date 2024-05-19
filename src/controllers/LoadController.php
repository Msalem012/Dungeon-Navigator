<?php

namespace task\controllers;

use task1\loader\JsonDungeonLoader;
use task1\objects\dungeon\Dungeon;

require_once ROOT_DIR . DS . 'src' . DS . 'loader' . DS . 'JsonDungeonLoader.php';

/**
 * Загрузчик лабиринта
 */
class LoadController
{
    /**
     * @var LoadController|null
     */
    private static $loadController = null;

    /**
     * @return LoadController
     */
    public static function getInstance(): LoadController
    {
        if (is_null(self::$loadController)) {
            self::$loadController = new LoadController();
        }

        return self::$loadController;
    }

    /**
     * @param $url
     * @return Dungeon|null
     */
    public function loadByUrl($url):?Dungeon {
        echo "Загружаю подземелье: $url" . ENDL;
        $loader = new JsonDungeonLoader($url);
        return $loader->load();
    }

    /**
     * @param $index
     * @return Dungeon|null
     */
    public function loadByIndex($index):?Dungeon {
        $url = DUNGEONS_DIR . DS . "dungeon$index.json";
        echo "Загружаю подземелье: $url" . ENDL;

        $loader = new JsonDungeonLoader($url);
        return $loader->load();
    }
}
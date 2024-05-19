<?php

namespace task1\loader;

use task1\objects\dungeon\Dungeon;

require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'dungeon' . DS . 'Dungeon.php';

/**
 * Интерфейс загрузчика подъемелья
 */
interface IDungeonLoader
{
    /**
     * Загрузить подземелье
     * @return Dungeon|null
     */
    public function load():?Dungeon;
}
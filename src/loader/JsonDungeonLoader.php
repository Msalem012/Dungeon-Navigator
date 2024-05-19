<?php

namespace task1\loader;

use task1\objects\dungeon\Dungeon;
use task1\objects\room\Monster;
use task1\objects\room\Room;
use task1\objects\room\Treasure;

require_once __DIR__ . DS . 'IDungeonLoader.php';
require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'dungeon' . DS . 'Dungeon.php';
require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'room' . DS . 'Room.php';
require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'room' . DS . 'Treasure.php';
require_once ROOT_DIR . DS . 'src' . DS . 'objects' . DS . 'room' . DS . 'Monster.php';

/**
 * Загрузчик JSON формата подземелья
 */
class JsonDungeonLoader implements IDungeonLoader
{
    private $url;

    /**
     * @param string $url путь к файлу с подземельем (может быть как локальный так и url ссылка)
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param $rooms
     * @param $easyWay
     * @return Dungeon
     */
    private function createDungeon($rooms, $easyWay):Dungeon {
        $dungeon = new Dungeon($rooms, 0, $easyWay);
        return $dungeon;
    }

    /**
     * @param $jsonString
     * @return Dungeon|null
     */
    private function parseJson($jsonString):?Dungeon {
        $json = json_decode($jsonString, true);
        if (!empty($json)) {
            $rooms = [];

            if ($json["rooms"] && $json["rooms"][0]) {
                if (DEBUG) echo "rooms tags found" . ENDL;

                $jsonRooms = $json["rooms"][0];
                foreach ($jsonRooms as $index => $jsonRoom) {
                    if (DEBUG) echo "loading room with index: $index" . ENDL;

                    $doors = [];
                    if (isset($jsonRoom["doors"])) {
                        if (DEBUG) echo "room with index: $index | doors found" . ENDL;
                        $doors = $jsonRoom["doors"];
                    }

                    $treasure = null;
                    if (isset($jsonRoom["treasure"])) {
                        if (DEBUG) echo "room with index: $index | treasure found" . ENDL;
                        $level = $jsonRoom["treasure"];
                        $treasure = new Treasure($level);
                    }

                    $monster = null;
                    if (isset($jsonRoom["monster"])) {
                        if (DEBUG) echo "room with index: $index | monster found" . ENDL;
                        $level = $jsonRoom["monster"];
                        $monster = new Monster($level);
                    }

                    $room = new Room($index, $doors, $treasure, $monster);
                    $rooms[] = $room;
                }
            }

            $easyWay = null;
            if (isset($json["easyway"])) {
                if (DEBUG) echo "easyway found" . ENDL;
                $easyWay = $json["easyway"];
            }

            return $this->createDungeon($rooms, $easyWay);
        }

        return null;
    }

    /**
     * Загрузка подземелья
     * @return Dungeon|null
     */
    public function load():?Dungeon
    {
        try {
            $jsonString = file_get_contents($this->url);
            if (DEBUG) echo "loaded jsonString: $jsonString" . ENDL;

            if ($jsonString) {
                return $this->parseJson($jsonString);
            } else {
                echo "Ошибка при загрузке подземелья";
            }
        } catch (\Exception $e) {
            echo "Ошибка при загрузке подземелья: {$e->getMessage()}\r\n";
            echo $e->getTraceAsString() . "\r\n";
        }

        return null;
    }
}
<?php

const DEBUG = false;

const ENDL = "\r\n";
const GAME_DELIMITER = "++++++++++++++++++";

const PLAYER_START_POS = 0;
const PLAYER_END_POS = -1000;

const ROOT_DIR = __DIR__;

const SESSION_DIR = ROOT_DIR . DS . 'session';
const SESSION_FILE = SESSION_DIR . DS . 'session.data';

const DUNGEONS_DIR = ROOT_DIR . DS . 'dungeons';

/**
 * Уровень сундуков
 * 1 и 2 значение минимальная и максимальная величина очков в сундуке
 */
const TREASURES_LEVELS = [
    1 => [1, 100],
    2 => [50, 1000],
    3 => [700, 10000]
];

/**
 * Уровни монстров
 * 1 и 2 значение минимальная и максимальная сила
 * 3 число модификатор на который уменьшается сила
 */
const MONSTER_LEVELS = [
    1 => [1, 50, 1],
    2 => [50, 250, 0.75],
    3 => [270, 1000, 0.5]
];

const MIN_PLAYER_POWER = 0;
const MAX_PLAYER_POWER = 100;

const RESULT_DIR = __DIR__ . DS . 'result';


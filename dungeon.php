<?php

use task\controllers\CommandController;

const DS = DIRECTORY_SEPARATOR;

require_once __DIR__ . DS . 'conf.php';
require_once __DIR__ . DS . 'src' . DS . 'controllers' . DS . 'CommandController.php';

CommandController::getInstance()->processCommand($argv);
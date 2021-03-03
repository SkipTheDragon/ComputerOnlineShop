<?php
session_start();

use Core\Core;
use Core\Request;

require 'config.php';

spl_autoload_register(static function ($class_name) {
    $class_path = CONFIG["PATHS"]["ROOT"].'/src/'.str_replace("\\","/",$class_name) . ".php";
    if (file_exists($class_path)) {
        require $class_path;
    } else {
        throw new \RuntimeException("CANNOT LOAD $class_name");
    }

});
$request = new Request(new Core(), $_POST, $_GET);


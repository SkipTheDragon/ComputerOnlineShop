<?php
session_start();

use Core\Core;
use Core\Request;

require 'config.php';

require __DIR__ . '/vendor/autoload.php';

$request = new Request(new Core(), $_POST, $_GET);
$request->route();

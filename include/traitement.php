<?php
include_once('conf/config.inc.php');
session_start();

spl_autoload_register(function ($classe) {

    $filePath = SERVER_ROOT_URL . "/class/class." . $classe . ".php";
    if (file_exists($filePath)) {
        require_once $filePath;
    }
});

$info = Information::getInstance();
var_dump($info);

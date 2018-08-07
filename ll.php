<?php require_once __DIR__ . '/autoload.php';


$argv[1] = ((isset($argv[1]))? $argv[1]: 'help');
if(class_exists($argv[1])){

    $comand = new $argv[1]($argv);
    $comand->rum();

} else echo 'comando não implementado';
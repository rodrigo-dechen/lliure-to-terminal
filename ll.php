<?php

require_once __DIR__ . '/vendor/autoload.php';

$comand = $argv[1] ?? 'help';
$comand = ucfirst(strtolower($comand));
$comand = "Commands\\{$comand}\\{$comand}";

if(!class_exists($comand)){
    $climate = new League\CLImate\CLImate;
    $climate->error('comando não implementado.');
    exit();
}

try {
    (new $comand($argv))->rum();
}catch (Exception $e){
    echo '[' . $e->getCode() . '] ' . $e->getFile() . ':' . $e->getLine() . "\n";
    echo $e->getMessage() . "\n";
    echo implode("\n", $e->getTrace());
}
<?php

require_once __DIR__ . '/vendor/autoload.php';

$comand = $argv[1] ?? 'help';
$comand = ucfirst(strtolower($comand));
$comand = "Commands\\{$comand}\\{$comand}";

if(!class_exists($comand)){
	echo 'comando não implementado';
}

$comand = new $comand($argv);
$comand->rum();
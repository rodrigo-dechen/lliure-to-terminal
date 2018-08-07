<?php

function autoload($nome){
    
    $nome = str_replace('\\/', DIRECTORY_SEPARATOR, $nome);
    $file = basename($nome); $path = dirname($nome);
    
    $f = realpath(__DIR__ . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . '.php');
    if(empty($f)) $f = realpath(__DIR__ . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $file . '.php');
    
    if(!empty($f) && file_exists($f)) require_once $f;
}

if(version_compare(PHP_VERSION, '5.1.2', '>=') && function_exists('spl_autoload_register')){
	if(version_compare(PHP_VERSION, '5.3.0', '>=')){
		spl_autoload_register('autoload', true, true);
	}else{
		spl_autoload_register('autoload');
	}
}else{
	function __autoload($nome){
		autoload($nome);
	}
}
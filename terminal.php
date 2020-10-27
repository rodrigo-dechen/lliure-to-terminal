<?php

abstract class terminal{

    public $path = '';
    public $comand = '';
    public $args = [];

    public function __construct($argv){
        $this->path = getcwd();
        array_shift($argv);
        $this->comand = $argv[0];
        array_shift($argv);
        $this->args = $argv;
    }

    public static function gets($format, ...$args){
        self::printr($format, ...$args);

        $input = fopen('php://stdin', 'r');
        $ter = trim(fgets($input));
        fclose($input);

        return $ter;
    }

    public static function printr($format, ...$args){
        echo sprintf($format, ...$args);
    }

    abstract public function rum();
}
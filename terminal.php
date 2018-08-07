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

    public static function gets($text = ''){
        $argv = func_get_args();
        call_user_func_array('terminal::printr', $argv);

        $input = fopen('php://stdin', 'r');
        $ter = trim(fgets($input));
        fclose($input);

        return $ter;
    }

    public static function printr($text){
        $argv = func_get_args();
        $text = array_shift($argv);
        foreach($argv as $v) $text = sprintf($text, $v);
        echo $text;
    }

    abstract public function rum();
}
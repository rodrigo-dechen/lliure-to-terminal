<?php

abstract class Terminal{

    public $path = '';
    public $comand = '';
    public array $args = [];

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

    /**
     * @param string|array $key
     * @param int $sequence
     * @return bool|array
     */
    protected function getExiteAndRemove($key, int $sequence = 1){
        if(!is_array($key)) $key = [$key];

        foreach($key as $search){
            if((($k = array_search($search, $this->args)) !== false)){
                $return[] = $this->args[$k];
                unset($this->args[$k]);

                if($sequence <= 1) return true;

				for($i = 1; $i < $sequence; $i++){
					if(isset($this->args[$k + $i])){
						$return[] = $this->args[$k + $i];
						unset($this->args[$k]);
					}
				}

                return $return;
            }
        }

        return false;
    }
}
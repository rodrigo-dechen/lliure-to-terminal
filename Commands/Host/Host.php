<?php


namespace Commands\Host;


class Host extends \Terminal
{


	public function rum(){
		if($list = (($k = array_search('-l', $this->args)) !== false)) unset($this->args[$k]);

		$fileHosts = 'C:\Windows\System32\drivers\etc\hosts';
		$hosts = self::getHosts($fileHosts);
		
		var_dump($hosts);
	}
	
	private static function getHosts($fileHosts){
		$hosts = [];
		$file = fopen($fileHosts, 'r');
		
		$index = 0;
		while(! feof($file)){
			$line = trim(fgets($file));
			if(empty($line) || !isset($line[0]) || $line[0] == '#'){
				continue;
			}

			$line = preg_split('/\s/', $line);
			$line = array_values((array) array_filter($line));
			
			$hosts[] = $line;
		}
		
		return $hosts;
	}
	
	
}
<?php


namespace Commands\Host;


class Host extends \Terminal
{

	private function help(){
		self::printr(implode(PHP_EOL, [
			"Gestor de hosts no windows",
			"",
			"Uso: ll host [[-h] | [host] [-d|-n]]",
			"",
			"\t-h",
			"\t\tLista os parametros disponiveis",
			"",
			"\t-n",
			"\t\tCriando novo host",
			"",
			"\t-d",
			"\t\tDeletando host",
			"",
			"\thost",
			"\t\tHost buscado, crido ou deletado",
		]));
	}

	public function rum(){
		if ($this->getExiteAndRemove('-h'))
			return $this->help();

		$new = $this->getExiteAndRemove('-n');
		$del = $this->getExiteAndRemove('-d');
		
		$fileHosts = 'C:\Windows\System32\drivers\etc\hosts';
		[$hosts, $lines] = self::explodeHost(self::getHosts($fileHosts));
		$run = 'all';

		$search = $this->args[0] ?? false;

		$run = $search !== false ? 'search': $run;
		$run = $new === true ? 'new': $run;
		$run = $del === true ? 'delete': $run;


		switch($run){
			case 'all':
				self::outputTable($hosts);
			break;
			case 'search':
				if($search !== false){
					self::outputTable(self::search($search, $hosts));
				}
			break;
			case 'new':
				if($search !== false){
					if(self::findHost($search, $hosts) === null){
						self::outputMsg("Host {$search} já existe!", 'error');
						break;
					}
					
					while(in_array($p = $this->gets("Deseja criar o hosts {$search}? [s/n]"), ['s', 'n']) === false);
					if($p == 'n') break;

					self::putHosts($fileHosts, self::newHost($search, $lines));
					self::outputMsg("Host {$search} adicionado com sucesso!");
				}
			break;
			case 'delete':
				if($search !== false){
					$delete = self::search($search, $hosts);
					
					if(empty($delete)){
						self::outputMsg("Host {$search} não encontrado", 'error');
						break;
					}
					
					self::outputTable($delete);

					while(in_array($p = $this->gets('Deseja remover todos os hosts a cima? [s/n]'), ['s', 'n']) === false);

					if($p == 'n') break;
					
					self::putHosts($fileHosts, self::deleteHost($delete, $lines));
					self::outputMsg("Host(s) removido(s)  com sucesso!");
				}
			break;
		}
	}

	public static function findHost($host, array $hosts): ?array{
		foreach($hosts as $line => [$ip, $name]){
			if($name === $name){
				return [$line, $ip, $name];
			}
		}
		return null;
	}

	private static function search($search, array $hosts): array{
		return array_filter($hosts, function($host) use ($search){
			return (strpos($host[1], $search) !== false);
		});
	}

	private static function getHosts($fileHosts): string{
		return file_get_contents($fileHosts);
	}

	private static function putHosts($fileHosts, $hosts): string{
		file_put_contents($fileHosts, implode("\n", $hosts));
		return self::getHosts($fileHosts);
	}

	private static function explodeHost(string $host): array{
		$lines = preg_split('/\n\r|\n|\r/', $host);

		$hosts = [];
		foreach($lines as $index => &$line){
			$line = trim($line);
			$tLine = rtrim($line, "\n\r");

			if(empty($tLine) || !isset($tLine[0]) || $tLine[0] == '#'){
				continue;
			}

			$tLine = preg_split('/\s/', $tLine);
			$tLine = array_values((array) array_filter($tLine));

			$hosts[$index] = $tLine;
		}

		return [$hosts, $lines];
	}

	private static function newHost($newHost, $lines): array{
		$lines[] = "127.0.0.1       {$newHost}";
		return $lines;
	}

	private static function deleteHost(array $delete, array $lines): array{
		foreach($delete as $line => $host){
			unset($lines[$line]);
		}
		return $lines;
	}

	private static function outputTable($hosts){
		$climate = new \League\CLImate\CLImate;
		empty($hosts)? $climate->info('Não localizados host para seu filtro') : $climate->table($hosts);
	}

	private static function outputMsg($string, string $type = 'info'): void{
		$climate = new \League\CLImate\CLImate;
		$climate->{$type}($string);
	}

}
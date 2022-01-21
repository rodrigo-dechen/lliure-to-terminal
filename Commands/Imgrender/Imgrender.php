<?php

namespace Commands\Imgrender;

use Terminal;

class Imgrender extends Terminal
{

	private bool         $log  = false;
	private static array $type = ['png' => 'image/webp', 'webp' => 'image/webp', 'jpg' => 'image/jpg'];

	public function help(){
		self::printr(implode(PHP_EOL, [
			"Renderizador de imagens",
			"\t-to\t\tTipo da saida [png, webp, jpg]",
			"\t-d [x] [y]\tDimensões maxima da imagem"
		]));
	}

	public function rum(){
		if ($this->getExiteAndRemove('-h')) return $this->help();

		/** @TODO Construir uma opção Search para filtrar nomes de arquivos */
		// Filtrando arquivos
		// if ($this->getExiteAndRemove('-s'));

		/** @TODO Construir a opção de executar nos sub diretórios */
		// recursive files
		// if ($this->getExiteAndRemove('-rf'));

		/** @TODO Construir a opção de remover imagens originais */
		// remove original
		// if ($this->getExiteAndRemove('-ro'));

		// type out
		[, $typeOut] = $this->getExiteAndRemove('-to', 2);
		if(!!$typeOut && !isset(self::$type[$typeOut])){
			self::printr('Tipo da saida não permitido');
			exit();
		}
		if(empty($typeOut)){
			$typeOut = 'webp';
		}

		// Dimensiona
		$dimensions = $this->getExiteAndRemove('-d', 3);
		$width = $dimensions[1] ?? null;
		$height = $dimensions[2] ?? null;
		
		$files = [];
		foreach(scandir($this->path) as $filename){
			$file = $this->path . DIRECTORY_SEPARATOR . $filename;
			$mimetype = mime_content_type($file);
			if($mimetype == 'image/' . $typeOut || !(substr($mimetype, 0, 6) == 'image/')){
				continue;
			}
			$files[] = [
				'name' => $file,
				'type' => $mimetype,
				'file' => base64_encode(file_get_contents($file)),
			];
		}

		if(!empty($files)){
			$files = self::cut($files, $width, $height, 'p', $typeOut);
			
			foreach($files as ['name' => $file, 'file' => $content]){
				file_put_contents($file, base64_decode($content));
			}
		}
	}

	private static function cut(array $files, ?int $width = null, ?int $height = null, string $type = 'p', ?string $renderOut = null)
	{
		if($renderOut !== null && !isset(self::$type[$renderOut])){
			$renderOut = false;
		}

		$return = [];

		// Cria uma nova imagem a partir de um arquivo ou URL
		foreach($files as $k => $image){

			if($renderOut !== false){
				$imgExt = $renderOut;
				$image['type'] = self::$type[$renderOut];
			}else{
				$imgExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
			}

			$image['file'] = base64_decode($image['file']);
			
			try{
				$oriImg = imagecreatefromstring($image['file']);
			}catch(\Exception $e){
				continue;
			}
			
			if($imgExt == 'webp' || $imgExt == 'png' || $imgExt == 'gif'){
				imagealphablending($oriImg, false);
				imagesavealpha($oriImg, true);
			}
			
			$oriWid = ImagesX($oriImg);
			$oriHei = ImagesY($oriImg);
			
			$widthFinal = $width ?? $oriWid;
			$heightFinal = $height ?? $oriHei;
			
			$heightFinal = ($heightFinal < 1? 1: $heightFinal);
			
			$basPro = $widthFinal / $heightFinal;
			$oriPro = $oriWid / $oriHei;
			
			$indRed = $heightFinal / $oriHei;
			
			$novLef = 0;
			$novTop = 0;
			
			$novWid = $widthFinal;
			$novHei = $heightFinal;
			
			switch($type == 'x' && $heightFinal < $oriHei && $widthFinal < $oriWid? 'c': $type){
				case 'c':
				case 'r':
					if($basPro > $oriPro)
						$indRed = $widthFinal / $oriWid;
					
					$novWid = $oriWid * $indRed;
					$novHei = $oriHei * $indRed;
					
					if($type == 'r'){
						$widthFinal = $novWid;
						$heightFinal = $novHei;
					}else{
						$novLef = ($widthFinal - $novWid) / 2;
						$novTop = ($heightFinal - $novHei) / 2;
					}
				
				break;
				
				case 'x':
					if($heightFinal > $oriHei && $widthFinal > $oriWid){
						$heightFinal = $oriHei;
						$widthFinal = $oriWid;
					}else{
						if($basPro < $oriPro){
							$heightFinal = $oriHei;
							
						}else{
							$widthFinal = $oriWid;
							
						}
					}
					
					$novWid = $oriWid;
					$novHei = $oriHei;
					
					$novLef = ($widthFinal - $novWid) / 2;
					$novTop = ($heightFinal - $novHei) / 2;
				
				break;
				
				case 'o':
					$imgExt = 'png';
				case 'p':
					if($basPro < $oriPro)
						$indRed = $widthFinal / $oriWid;
					
					$novWid = $oriWid * $indRed;
					$novHei = $oriHei * $indRed;
					
					if($type == 'p'){
						$widthFinal = $novWid;
						$heightFinal = $novHei;
					}else{
						$novLef = ($widthFinal - $novWid) / 2;
						$novTop = ($heightFinal - $novHei) / 2;
					}
				break;
			}
			
			$newImg = imagecreatetruecolor($widthFinal, $heightFinal);
			
			ob_start();
			switch($imgExt){
				case 'jpg':
					imagecopyresampled($newImg, $oriImg, $novLef, $novTop, 0, 0, $novWid, $novHei, $oriWid, $oriHei);
					imagejpeg($newImg, null, 100);
				break;
				
				case 'png':
				case 'gif':
				case 'webp':
					imagealphablending($newImg, false);
					$corTra = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
					imagefill($newImg, 0, 0, $corTra);
					imagesavealpha($newImg, true);
					imagealphablending($newImg, true);
					
					imagecopyresampled($newImg, $oriImg, $novLef, $novTop, 0, 0, $novWid, $novHei, $oriWid, $oriHei);
					
					switch($imgExt){
						case 'png':
							imagepng($newImg, null);
						break;
						case 'gif':
							imagegif($newImg, null);
						break;
						case 'webp':
							imagewebp($newImg, null, 80);
						break;
					}
				break;
			}
			imagedestroy($newImg);
			$image_data = base64_encode(ob_get_clean());
			
			['dirname' => $dirname, 'filename' => $filename] = pathinfo($image['name']);
			$image['name'] = $dirname . DIRECTORY_SEPARATOR . $filename . '.' . $imgExt;
			
			$return[$k] = [
				'name' => $image['name'],
				'type' => $image['type'],
				'file' => $image_data,
			];
		}
		
		return $return;
	}
}
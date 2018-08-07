<?php

class create extends terminal{

    public function rum(){

        $model = 'defalt';
        if($log = (($k = array_search('-l', $this->args)) !== false)) unset($this->args[$k]);
        if(($k = array_search('-m', $this->args)) !== false && isset($this->args[($k + 1)])){
            $model = $this->args[($k + 1)];
            unset($this->args[$k], $this->args[($k + 1)]);
            $this->args = array_merge([], $this->args);
        }

        if(!isset($this->args[0])) $this->args[0] = $this->gets('Digite a pasta destino > ');
        $newpath = $this->args[0];
        $newpath = self::definiDir($newpath);


        $pathModel = __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $model;
        
        if(!file_exists($pathModel)){
            self::printr('Este modelo não foi implementado'); return;}

        $this->criando($model, $pathModel, $newpath, $log);
    }

    private function criando($key, $pathMod, $pathNew, $log = false){

        if(!file_exists($pathNew)){
            if($log) self::printr("Criando diretorio > " . basename($pathNew) . "\n");
            mkdir($pathNew);}

        foreach(scandir($pathMod) as $f){
            if($f == '.' || $f == '..') continue;

            $opf = $pathMod . DIRECTORY_SEPARATOR . $f;
            $dpf = $pathNew . DIRECTORY_SEPARATOR . $f;

            if(is_dir($opf)){
                self::criando($key, $opf, $dpf, $log);

            }else{
                $ext = '';
                $oldname = basename($pathMod);
                $newname = basename($pathNew);
                if(preg_match('/^' . preg_quote($oldname) . '/', $f))
                    $dpf = $pathNew . DIRECTORY_SEPARATOR . $newname . ($ext = substr($f, strlen($oldname)));

                if(!file_exists($dpf)){
                    $content = file_get_contents($opf);
                    $content = explode('{' . (strtolower($key)) . '}', $content);
                    $content = implode($newname, $content);

                    $content = explode('{' . ucfirst(strtolower($key)) . '}', $content);
                    $content = implode(ucfirst(strtolower($newname)), $content);

                    file_put_contents($dpf, $content);

                    if($log) self::printr("Criando arquivo > {$newname}{$ext}\n");
                }
            }
        }


    }

    private function definiDir($dir){
        $dir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir);
        $teste = parse_url($dir);
        if($teste['path'][0] !== DIRECTORY_SEPARATOR) $dir = $this->path . DIRECTORY_SEPARATOR . $dir;
        return $dir;
    }

}
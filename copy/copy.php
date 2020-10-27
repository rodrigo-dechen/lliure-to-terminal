<?php

class copy extends terminal{
    private bool $log = false;

    public function help(){
        self::printr(implode(PHP_EOL, [
            'Copia arquivo(s) e pastas podendo modificar altomaticamente seu conteudo',
            'O original pode ter o caracter "*" (coringa) no final, com isso ira copiar todos os arquivos que comecem com o original',
            '',
            "\t-l\tmostra um log interno do script",
            "\t-r\ttroca as ocorencias no conteudo do nome original no destino ",
            "\t-ed\tdesconsidera os diretorios",
            "\t-ef\tdesconsidera os arquivos",
        ]));
    }

    public function rum(){
        if(array_search('-h', $this->args) !== false) return $this->help();

        if($this->log = (($k = array_search('-l', $this->args)) !== false)) unset($this->args[$k]);
        if($repalce = (($k = array_search('-r', $this->args)) !== false)) unset($this->args[$k]);
        if($excludeDirectory = (($k = array_search('-ed', $this->args)) !== false)) unset($this->args[$k]);
        if($excludeFile = (($k = array_search('-ef', $this->args)) !== false)) unset($this->args[$k]);

        while(!isset($this->args[0]) || !self::sourceExists($this->absolutePath($this->args[0]))){
            self::printr('Original não encontado' . ((isset($this->args[0]))? ': '. $this->absolutePath($this->args[0]): '') . PHP_EOL);
            $this->args[0] = $this->gets('Digite o original > ');
        }

        if(!isset($this->args[1]))
            $this->args[1] = $this->gets('Digite a copia > ');

        while(self::copyExists($this->absolutePath($this->args[0]), $this->absolutePath($this->args[1]))){
            if($this->gets('Copia existe. Sobresquevela (S ou N) > ') === 'S') break;
            $this->args[1] = $this->gets('Digite a copia > ');
        }

        self::copyPath($this->absolutePath($this->args[0]), $this->absolutePath($this->args[1]), $repalce, $excludeFile, $excludeDirectory);
    }

    private function copyPath($source, $destiny, $repalce = false, $excludeFile = false, $excludeDirectory = false){
        $generic = self::generic($source, $source);
        $oPath = dirname($source);
        $oName = basename($source);
        $nPath = dirname($destiny);
        $nName = basename($destiny);

        $oFiles = [];
        if($generic){
            $regQry = '/^' . preg_quote($oName) . '/';
            foreach(scandir($oPath) as $fileName){
                if(!($fileName === '.' || $fileName === '..') && preg_match($regQry, $fileName)){
                    $oFiles[] = $oPath . DIRECTORY_SEPARATOR . $fileName;
                }
            }
        }else{
            $oFiles[] = $source;
        }

        foreach($oFiles as $k => $file){
            if(($excludeDirectory && is_dir($file)) || ($excludeFile && is_file($file))){
                unset($oFiles[$k]);
            }
        }

        foreach($oFiles as $k => $file){
            $copy = (($generic)? $nPath . DIRECTORY_SEPARATOR . $nName . substr(basename($file), strlen($oName)): $destiny);

            if(file_exists($copy)) continue;

            if(is_dir($file)){
                $this->copyDiretory($file, $copy, $repalce);
                continue;}

            if(!is_file($file)) continue;

            $this->copyFile($file, $copy);

            if($repalce && !self::isBinary($copy))
                $this->replaceContent($oName, $nName, $copy);
        }
    }

    private function copyDiretory($source, $destiny, $repalce = false){

        if($this->log) self::printr("Copiando diretorio:: {$source} >> {$destiny}" . PHP_EOL);
        if (!is_dir($destiny)) mkdir($destiny, 0755);

        $oldname = basename($source);
        $newname = basename($destiny);

        foreach(scandir($source) as $f){
            if ($f == '.' || $f == '..') continue;

            $target = false;
            $file = ($source . DIRECTORY_SEPARATOR . $f);

            if (is_dir($file)){
                $this->copyDiretory($file, ($destiny . DIRECTORY_SEPARATOR . $f));
                continue;}

            if(!is_file($file)) continue;

            if(preg_match('/^' . preg_quote($oldname) . '/', $f))
                $copy = ($target = $destiny . DIRECTORY_SEPARATOR . $newname . substr($f, strlen($oldname)));
            else
                $copy = ($destiny . DIRECTORY_SEPARATOR . $f);

            if(!file_exists($copy))
                $this->copyFile($file, $copy);

            if($repalce && $target && !self::isBinary($target))
                $this->replaceContent($oldname, $newname, $target);
        }
    }

    private function copyFile(string $source, string $dest){
        if($this->log) self::printr("Copiando arquivo:: {$source} >> {$dest}" . PHP_EOL);
        return copy($source, $dest);
    }

    private function replaceContent($search, $replace, $subject){
        if($this->log) self::printr("Subistituindio:: [{$search}] >> [{$replace}] :: {$subject}" . PHP_EOL);
        file_put_contents($subject, implode($replace, explode($search, file_get_contents($subject))));
    }

    private function absolutePath ($arg){
        $teste = parse_url($arg);
        if(!isset($teste['scheme']))
            return $this->path . DIRECTORY_SEPARATOR . $arg;
        return $arg;
    }

    private static function sourceExists($old){
        $generic = self::generic($old, $old);

        $path = dirname($old);
        $name = basename($old);

        if(!$generic && file_exists($old)) return true;

        $regQry = '/^' . preg_quote($name) . '/';
        foreach(scandir($path) as $f){
            if($f == '.' || $f == '..') continue;
            if(preg_match($regQry, $f)) return true;
        } return false;
    }

    private static function generic($path, &$clear = ''){
        if (substr($path, -1) !== '*') return false;
        $clear = substr($clear, 0, -1);
        return true;
    }

    private static function copyExists($old, $new){
        $generic = self::generic($old, $old);

        if(!$generic && file_exists($new)) return true;

        $oPath = dirname($old);
        $oName = basename($old);
        $nPath = dirname($new);
        $nName = basename($new);
        $nRefs = [];

        $regQry = '/^' . preg_quote($oName) . '(.*)/';
        foreach(scandir($oPath) as $fileName) if(!($fileName === '.' || $fileName === '..') && preg_match($regQry, $fileName, $matches) && isset($matches[1]))
            $nRefs[] = preg_quote($nName . $matches[1]);

        $regQry = '/^' . implode('$|^', $nRefs) . '$/';
        foreach(scandir($nPath) as $fileName) if(!($fileName === '.' || $fileName === '..') && preg_match($regQry, $fileName))
            return true;

        return false;
    }

    private static function isBinary($filename){
        return (substr(finfo_file(finfo_open(FILEINFO_MIME), $filename), 0, 4) !== 'text');
    }
}
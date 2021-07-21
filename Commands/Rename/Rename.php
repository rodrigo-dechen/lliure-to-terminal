<?php

namespace Commands\Rename;

use Terminal;

class Rename extends Terminal {
    public function rum(){

        if($log = (($k = array_search('-l', $this->args)) !== false)) unset($this->args[$k]);
        if($repalce = (($k = array_search('-r', $this->args)) !== false)) unset($this->args[$k]);

        $this->args[0] = $this->validaDiretorio(((isset($this->args[0]))? $this->args[0]: null));
        if($this->args[0] === false ) return;

        if(!isset($this->args[1])) $this->args[1] = $this->gets('Digite a pasta destino > ');

        $oldname = $oldpath = $this->args[0];
        $newname = $newpath = $this->args[1];

        $teste = parse_url($oldname);
        if(!isset($teste['scheme'])) $oldpath = $this->path . DIRECTORY_SEPARATOR . $oldname;

        $teste = parse_url($newname);
        if(!isset($teste['scheme'])) $newpath = $this->path . DIRECTORY_SEPARATOR . $newname;

        $oldname = basename($oldpath);
        $newname = basename($newpath);

        foreach(scandir($oldpath) as $f){
            if(is_dir($oldpath . DIRECTORY_SEPARATOR . $f)) continue;

            if($repalce) file_put_contents($oldpath . DIRECTORY_SEPARATOR . $f, implode($newname, explode($oldname, file_get_contents($oldpath . DIRECTORY_SEPARATOR . $f))));

            if(preg_match('/^' . preg_quote($oldname) . '/', $f)){
                $ext = substr($f, strlen($oldname));
                rename(
                    $oldpath . DIRECTORY_SEPARATOR . $f,
                    $oldpath . DIRECTORY_SEPARATOR . $newname . $ext
                );
                if($log) self::printr("renomeado diretorio: {$f} > {$newname}{$ext}\n");
            }
        }

        rename( $oldpath, $newpath );
        if($log) self::printr("renomeado diretorio: {$oldname} > {$newname}\n");
    }


    private function validaDiretorio($dir = null){
        while(empty($dir)){
            $dir = $this->gets('Digite a pasta origem > ');
        }

        if($dir == "'") return false;

        $dir = $this->definiDir($dir);

        if(!is_dir($dir)){
            self::printr("Esta entrada não corresponde a um diretorio\n");
            return $this->validaDiretorio();
        }

        return $dir;
    }

    private function definiDir($dir){
        $dir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir);
        $teste = parse_url($dir);
        if($teste['path'][0] !== DIRECTORY_SEPARATOR) $dir = $this->path . DIRECTORY_SEPARATOR . $dir;
        return $dir;
    }

}
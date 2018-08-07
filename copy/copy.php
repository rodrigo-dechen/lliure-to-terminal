<?php

class copy extends terminal{
    public function rum(){

        if($log = (($k = array_search('-l', $this->args)) !== false)) unset($this->args[$k]);


        if(!isset($this->args[0])){
            do{
                $this->args[0] = $this->gets('Digite a pasta origem > ');

                $f = $teste = parse_url($this->args[0]);
                if(!isset($teste['scheme'])) $f = $this->path . DIRECTORY_SEPARATOR . $this->args[0];

            }while(!file_exists($f));
        }


        if(!isset($this->args[1])) $this->args[1] = $this->gets('Digite a pasta destino > ');

        $oldname = $oldpath = $this->args[0];
        $newname = $newpath = $this->args[1];

        $teste = parse_url($oldname);
        if(!isset($teste['scheme'])) $oldpath = $this->path . DIRECTORY_SEPARATOR . $oldname;

        $teste = parse_url($newname);
        if(!isset($teste['scheme'])) $newpath = $this->path . DIRECTORY_SEPARATOR . $newname;

        self::copyPath($oldpath, $newpath, $log);
    }

    private static function copyPath($oldpath, $newpath, $log = false){
        if(!is_dir($oldpath)){
            self::printr('Diretorio origem não encontado: ' . $oldpath);
            return;
        }

        if (!is_dir($newpath)) mkdir($newpath, 0755);

        $oldname = basename($oldpath);
        $newname = basename($newpath);

        foreach(scandir($oldpath) as $f){
            if ($f == '.' || $f == '..') continue;

            if (is_dir($oldpath . DIRECTORY_SEPARATOR . $f)){
                if($log) self::printr("Copiando diretorio > $f\n");
                self::copyPath($oldpath . DIRECTORY_SEPARATOR . $f, $newpath . DIRECTORY_SEPARATOR . $f);

            }else{
                if(preg_match('/^' . preg_quote($oldname) . '/', $f)){
                    $ext = substr($f, strlen($oldname));
                    if(!file_exists($newpath . DIRECTORY_SEPARATOR . $f)
                    &&(!file_exists($newpath . DIRECTORY_SEPARATOR . $newname . $ext))){
                        if($log) self::printr("Copiando arquivo > $f\n");
                        copy(
                            $oldpath . DIRECTORY_SEPARATOR . $f,
                            $newpath . DIRECTORY_SEPARATOR . $newname . $ext
                        );
                    }
                }else{
                    if($log) self::printr("Copiando arquivo > $f\n");
                    copy(
                        $oldpath . DIRECTORY_SEPARATOR . $f,
                        $newpath . DIRECTORY_SEPARATOR . $f
                    );
                }
            }
        }
    }

}
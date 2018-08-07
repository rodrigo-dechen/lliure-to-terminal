<?php

class help extends terminal{
    public function rum(){
        echo implode("\n", [
            "     ___     ___     ___  ___   ___  _______    _______",
            "    /  /    /  /    /__/ /  /  /  / /  __   |  /  ____/",
            "   /  /    /  /    ___  /  /  /  / /  /_/  /  /  /___ ",
            "  /  /    /  /    /  / /  /  /  / /  ___  (  /  ____/",
            " /  /__  /  /__  /  / /  /__/  / /  /  /  / /  /___ ",
            "/_____/ /_____/ /__/  \_______/ /__/  /__/ /______/",
            "lliure to terminal version 0.0.1 2018-08-02 20:31:59",
            "",
            "Esse é o lliure para terminal desenvolvido para auxiliar tarefas repetitivas no desenvilvimento na platafomar",
            "",
            "Voce esta rodando em: {$this->path}",
        ]);
    }
}
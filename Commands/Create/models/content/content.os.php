<?php global $form, $dados, $cms; /* @var $this aplimo */

switch(((isset($_GET['ac']))? $_GET['ac']: null)){
    case 'salvar':
        try{
            fields::salve($_POST);
            $fileUp = new fileup(); $fileUp->directory = PATH_CMS; $fileUp->up();

            if(isset($_POST['id'])) $cms->upd{Content}($_POST);
            else     $_POST['id'] = $cms->set{Content}($_POST);

            Vigile::success('Salvo cos sucesso');
            header('Location: '. $this->apm->home . '&id='. $_POST['id']);

        }catch(Exception $e){ $cms->queryLog(); }
    break;
}
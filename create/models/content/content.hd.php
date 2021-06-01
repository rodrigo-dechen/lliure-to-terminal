<?php global $aplimo, $cms, $form, $dados; /* @var $this aplimo */

ll::api('navigi');
ll::api('aplimo');
ll::opt('usrcontent');

$aplimo = new aplimo();

$aplimo->hdMenuLeft([
    $aplimo->hdMenuTitle('{Content}')
]);

$aplimo->hdMenuRigth([
    $aplimo->hdMenuA('Novo', $this->apm->home . '&id=new')
]);

$dados = [];
$id = ((isset($_GET['id']) && !empty($_GET['id']))? $_GET['id']: ((isset($_POST['id']))? $_POST['id']: false));

if($id !== false){
    $dados = DB::first($cms->get{Content}([['id' => (int) $id]]));
}

$form = [
    // campos do formulario
];

$form = new fields($form);
fields::header(PATH_CMS);
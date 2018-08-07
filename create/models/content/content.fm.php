<?php global $aplimo, $form, $dados; /* @var $this aplimo */ ?>

<form action="<?php echo $this->apm->onserver; ?>&ac=salvar" method="post" enctype="multipart/form-data">

    <?php if(isset($dados['id'])){ ?>
        <input type="hidden" <?php echo ll::montaInput('id', $dados); ?>>
    <?php } ?>

    <?php $form->form($dados); ?>

    <div class="text-right">
        <a class="btn btn-default" href="<?php echo $this->apm->home; ?>">Voltar</a>
        <button class="btn btn-primary" type="submit">Salvar</button>
    </div>

</form>

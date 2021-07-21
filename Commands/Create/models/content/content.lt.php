<?php

$navigi = new navigi();
$navigi->tabela = PREFIXO_CMS . '{{content}}';

$navigi->query = 'SELECT id, nome FROM ' . $navigi->tabela;
$navigi->order = ['id' => 'DESC'];

$navigi->delete = true;
$navigi->rename = false;
$navigi->exibicao = navigi::LISTA;
$navigi->paginacao = 20;
$navigi->config = [ 'link' => $this->apm->home . '&id=' ];

$navigi->pesquisa = 'id:int,nome';
$navigi->placeholder = 'COD, NOME';

//$navigi->debug = true;

$navigi->monta();
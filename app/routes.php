<?php
$routes = [];

$routes[] = ['/', 'AdminController@index'];
$routes[] = ['/recibo', 'AdminController@recibo'];

$routes[] = ['/usuario/index', 'UserController@index'];
$routes[] = ['/usuario/logar', 'UserController@logar'];
$routes[] = ['/usuario/sair', 'UserController@logout'];
$routes[] = ['/usuario/adicionar', 'UserController@adicionar'];
$routes[] = ['/usuario/salvar', 'UserController@salvar'];

$routes[] = ['/pessoa/painel', 'PessoaController@painel'];
$routes[] = ['/pessoa/index', 'PessoaController@index'];
$routes[] = ['/pessoa/adicionar', 'PessoaController@adicionar'];
$routes[] = ['/pessoa/salvar', 'PessoaController@salvar'];
$routes[] = ['/pessoa/editar', 'PessoaController@editar'];
$routes[] = ['/pessoa/info', 'PessoaController@info'];
$routes[] = ['/pessoa/deletar', 'PessoaController@deletar'];
$routes[] = ['/pessoa/atualizar', 'PessoaController@atualizar'];

$routes[] = ['/logradouro/index', 'LogradouroController@index'];
$routes[] = ['/logradouro/editar', 'LogradouroController@editar'];
$routes[] = ['/logradouro/atualizar', 'LogradouroController@atualizar'];
$routes[] = ['/logradouro/salvar', 'LogradouroController@salvar'];
$routes[] = ['/logradouro/info', 'LogradouroController@info'];
$routes[] = ['/logradouro/deletar', 'LogradouroController@deletar'];
$routes[] = ['/logradouro/load/cep', 'LogradouroController@loadCep'];


return $routes;
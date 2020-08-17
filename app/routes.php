<?php
$routes = [];

$routes[] = ['/', 'AdminController@index'];

$routes[] = ['/usuario/index', 'UserController@index'];
$routes[] = ['/usuario/logar', 'UserController@logar'];
$routes[] = ['/usuario/sair', 'UserController@logout'];
$routes[] = ['/usuario/adicionar', 'UserController@adicionar'];
$routes[] = ['/usuario/salvar', 'UserController@salvar'];

$routes[] = ['/pessoa/index', 'PessoaController@index'];
$routes[] = ['/pessoa/adicionar', 'PessoaController@adicionar'];
$routes[] = ['/pessoa/salvar', 'PessoaController@salvar'];
$routes[] = ['/pessoa/editar', 'PessoaController@editar'];
$routes[] = ['/pessoa/info', 'PessoaController@info'];
$routes[] = ['/pessoa/deletar', 'PessoaController@deletar'];


return $routes;
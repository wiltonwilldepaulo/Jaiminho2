<?php

use app\controller\User;
use app\controller\Home;
use app\controller\Customer;
use app\controller\Login;
use app\middleware\Middleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home')->add(Middleware::authentication());
$app->get('/home', Home::class . ':home')->add(Middleware::authentication());
$app->get('/login', Login::class . ':login');

$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('/precadastro', Login::class . ':precadastro');
    $group->post('/autenticar', Login::class . ':autenticar');
});
$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista')->add(Middleware::authentication());
    $group->get('/cadastro', User::class . ':cadastro')->add(Middleware::authentication());
    $group->get('/alterar/{id}', User::class . ':alterar');
    $group->post('/listuser', User::class . ':listuser');
    $group->post('/insert', User::class . ':insert');
    $group->post('/update', User::class . ':update');
});
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/lista', Customer::class . ':lista');
    $group->get('/cadastro', Customer::class . ':cadastro');
    $group->post('/insert', Customer::class . ':insert');
});

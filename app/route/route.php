<?php

use app\controller\User;
use app\controller\Home;
use app\controller\Customer;
use app\controller\Login;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home');

$app->get('/home', Home::class . ':home');
$app->get('/login', Login::class . ':login');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista');
    $group->get('/cadastro', User::class . ':cadastro');
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

<?php

use app\controller\User;
use app\controller\Home;
use app\controller\Customer;
use app\controller\Login;
use app\middleware\Middleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home')->add(Middleware::route());

$app->get('/home', Home::class . ':home')->add(Middleware::route());
$app->get('/login', Login::class . ':login');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista');
    $group->get('/cadastro', User::class . ':cadastro');
    $group->post('/listuser', User::class . ':listuser');
});
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/lista', Customer::class . ':lista');
    $group->get('/cadastro', Customer::class . ':cadastro');
    $group->post('/insert', Customer::class . ':insert');
});

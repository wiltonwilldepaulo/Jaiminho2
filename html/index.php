<?php

use app\database\builder\InsertQuery;

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$Values = [
    'nome_fantasia' => 'Gambiarra & CIA',
    'cpf_cnpj' => '123',
    'rg_ie' => '321'
];

InsertQuery::table('empresa')->save($Values);
die;

$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../app/helper/settings.php';
require __DIR__ . '/../app/route/route.php';

$app->run();

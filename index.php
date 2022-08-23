<?php

require_once(__DIR__ . '/vendor/autoload.php'); 

use App\Application;
use App\Controllers\NotebookController;
use App\Services\Database\Factories\NotebookContextFactory;
use App\Services\ImageSaver;
use App\Services\Validator;

$factoryContext = new NotebookContextFactory();

$controllers = [
    NotebookController::class => new NotebookController($factoryContext->create(), new ImageSaver(), new Validator),
];


$app = new Application($controllers);

$app->start();





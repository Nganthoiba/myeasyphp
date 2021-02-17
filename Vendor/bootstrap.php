<?php
require_once "autoload.php";
require_once dirname(__DIR__).'../Config/path.php';
require_once LIBS_PATH. 'special_functions.php';//loading global functions
require_once dirname(__DIR__).'../Config/app.php';



use MyEasyPHP\Libs\DoctrineEntityManager;
use Dotenv\Dotenv;
$env = Dotenv::createUnsafeImmutable(ROOT);
$env->load();
require_once dirname(__DIR__).'../Config/database.php';
// setup for Doctrine Entity Manager
$entityManager = DoctrineEntityManager::getEntityManager('Default');


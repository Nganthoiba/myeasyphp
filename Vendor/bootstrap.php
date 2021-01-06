<?php
require_once "autoload.php";
require_once dirname(__DIR__).'../Config/path.php';
require_once dirname(__DIR__).'../Config/app.php';

use MyEasyPHP\Libs\DoctrineEntityManager;
// setup for Doctrine Entity Manager
$entityManager = DoctrineEntityManager::getEntityManager();


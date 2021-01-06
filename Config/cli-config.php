<?php
// cli-config.php: Doctrine config file
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once dirname(__DIR__).'../Vendor/bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);



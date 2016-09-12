<?php

require_once __DIR__.'/../../../vendor-bin/doctrine/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = Setup::createAnnotationMetadataConfiguration(
    [
        __DIR__.'/../../../fixtures/Bridge/Doctrine/Entity',
    ],
    true
);

$entityManager = EntityManager::create(
    [
        'driver' => false !== getenv('DB_DRIVER')? getenv('DB_DRIVER') : 'pdo_mysql',
        'user' => false !== getenv('DB_USER')? getenv('DB_USER') : 'root',
        'password' => false !== getenv('DB_PASSWORD')? getenv('DB_PASSWORD') : null,
        'dbname' => false !== getenv('DB_NAME')? getenv('DB_NAME') : 'fidry_alice_data_fixtures',
    ],
    $config
);

$GLOBALS['entityManager'] = $entityManager;

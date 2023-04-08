<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Jayrods\ScubaPHP\Controller\Validation\UserValidator;
use Jayrods\ScubaPHP\Infrastructure\Database\SqlitePdoConnection;
use Jayrods\ScubaPHP\Repository\AdoptionRepository\SqliteAdoptionRepository;
use Jayrods\ScubaPHP\Repository\PetRepository\SqlitePetRepository;
use Jayrods\ScubaPHP\Repository\UserRepository\SqliteUserRepository;
use Jayrods\ScubaPHP\Service\MailService;
use PHPMailer\PHPMailer\PHPMailer;

$builder = new ContainerBuilder();

$builder->addDefinitions(array(
    SqliteUserRepository::class => function () {
        $sqlitePdoRepository = new SqlitePdoConnection();

        return new SqliteUserRepository($sqlitePdoRepository);
    },
    SqlitePetRepository::class => function () {
        $sqlitePdoRepository = new SqlitePdoConnection();

        return new SqlitePetRepository($sqlitePdoRepository);
    },
    SqliteAdoptionRepository::class => function () {
        $sqlitePdoRepository = new SqlitePdoConnection();

        return new SqliteAdoptionRepository($sqlitePdoRepository);
    },
    UserValidator::class => function () {
        $sqlitePdoRepository = new SqlitePdoConnection();
        $sqliteUserRepository = new SqliteUserRepository($sqlitePdoRepository);

        return new UserValidator($sqliteUserRepository);
    },
    MailService::class => function () {
        $mail = new PHPMailer(ENVIRONMENT === 'production' ? false : true);

        return new MailService($mail);
    }
));

/** @var \Psr\Container\ContainerInterface */
$container = $builder->build();

return $container;

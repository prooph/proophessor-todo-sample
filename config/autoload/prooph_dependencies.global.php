<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ProophessorDo;

use Prooph\EventStore;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * The services configuration is used to set up a Zend\ServiceManager
 * which is used as Inversion of Controller container in our application
 * Please refer to the official documentation:
 * http://framework.zend.com/manual/current/en/modules/zend.service-manager.html
 */
return [
    'dependencies' => [
        'invokables' => [
            \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class => \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class,
            // Aggregate Translator
            \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
        ],
        'factories' => [
            'pdo.connection' => EventStore\Pdo\Container\PdoConnectionFactory::class,
            \Prooph\Common\Messaging\NoOpMessageConverter::class => InvokableFactory::class,
            \Prooph\Common\Messaging\FQCNMessageFactory::class => InvokableFactory::class,
            \Prooph\ProophessorDo\Response\JsonResponse::class => InvokableFactory::class,
            // prooph/psr7-middleware set up
            \Prooph\Psr7Middleware\CommandMiddleware::class => \Prooph\Psr7Middleware\Container\CommandMiddlewareFactory::class,
            \Prooph\Psr7Middleware\EventMiddleware::class => \Prooph\Psr7Middleware\Container\EventMiddlewareFactory::class,
            \Prooph\Psr7Middleware\QueryMiddleware::class => \Prooph\Psr7Middleware\Container\QueryMiddlewareFactory::class,
            \Prooph\Psr7Middleware\MessageMiddleware::class => \Prooph\Psr7Middleware\Container\MessageMiddlewareFactory::class,
            //prooph/service-bus set up
            \Prooph\ServiceBus\CommandBus::class => \Prooph\ServiceBus\Container\CommandBusFactory::class,
            \Prooph\ServiceBus\EventBus::class => \Prooph\ServiceBus\Container\EventBusFactory::class,
            \Prooph\ServiceBus\QueryBus::class => \Prooph\ServiceBus\Container\QueryBusFactory::class,
            //prooph/event-store-bus-bridge set up
            \Prooph\EventStoreBusBridge\TransactionManager::class => \Prooph\EventStoreBusBridge\Container\TransactionManagerFactory::class,
            \Prooph\EventStoreBusBridge\EventPublisher::class => \Prooph\EventStoreBusBridge\Container\EventPublisherFactory::class,
            \Prooph\Cli\Console\Helper\ClassInfo::class => \Prooph\ProophessorDo\Container\Console\Psr4ClassInfoFactory::class,
            // persistence strategies
            EventStore\Pdo\PersistenceStrategy\MariaDbSingleStreamStrategy::class => InvokableFactory::class,
            EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy::class => InvokableFactory::class,
            EventStore\Pdo\PersistenceStrategy\PostgresSingleStreamStrategy::class => InvokableFactory::class,
        ],
    ],
];

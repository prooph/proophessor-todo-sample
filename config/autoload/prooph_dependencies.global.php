<?php

/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

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
            \Prooph\Common\Messaging\FQCNMessageFactory::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Prooph\ProophessorDo\Response\JsonResponse::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            // prooph/event-store set up
            \Prooph\EventStore\EventStore::class => \Prooph\EventStore\Container\EventStoreFactory::class,
            \Prooph\EventStore\Snapshot\SnapshotStore::class => \Prooph\EventStore\Container\Snapshot\SnapshotStoreFactory::class,
            //Default factories for the event store and snapshot adapters, depending on the installed adapter the event store factory
            //will use the configured adapter type to get an adapter instance from the service manager
            //to ease system set up we register both factories here so that the user doesn't need to worry about it
            'Prooph\\EventStore\\Adapter\\MongoDb\\MongoDbEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\MongoDb\\Container\\MongoDbEventStoreAdapterFactory',
            'Prooph\\EventStore\\Adapter\\Doctrine\\DoctrineEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\Doctrine\\Container\\DoctrineEventStoreAdapterFactory',
            'Prooph\\EventStore\\Snapshot\\Adapter\\Doctrine\\DoctrineSnapshotAdapter' => 'Prooph\\EventStore\\Snapshot\\Adapter\\Doctrine\\Container\\DoctrineSnapshotAdapterFactory',
            'Prooph\\EventStore\\Snapshot\\Adapter\\MongoDb\\MongoDbSnapshotAdapter' => 'Prooph\\EventStore\\Snapshot\\Adapter\\MongoDb\\Container\\MongoDbSnapshotAdapterFactory',
            'Prooph\\EventStore\\Snapshot\\Adapter\\Memcached\\MemcachedSnapshotAdapter' => 'Prooph\\EventStore\\Snapshot\\Adapter\\Memcached\\Container\\MemcachedSnapshotAdapterFactory',
            // prooph/psr7-middleware set up
            \Prooph\Psr7Middleware\CommandMiddleware::class => \Prooph\Psr7Middleware\Container\CommandMiddlewareFactory::class,
            \Prooph\Psr7Middleware\EventMiddleware::class => \Prooph\Psr7Middleware\Container\EventMiddlewareFactory::class,
            \Prooph\Psr7Middleware\QueryMiddleware::class => \Prooph\Psr7Middleware\Container\QueryMiddlewareFactory::class,
            \Prooph\Psr7Middleware\MessageMiddleware::class => \Prooph\Psr7Middleware\Container\MessageMiddlewareFactory::class,
            //prooph/service-bus set up
            \Prooph\ServiceBus\CommandBus::class => \Prooph\ServiceBus\Container\CommandBusFactory::class,
            \Prooph\ServiceBus\EventBus::class => \Prooph\ServiceBus\Container\EventBusFactory::class,
            \Prooph\ServiceBus\QueryBus::class => \Prooph\ServiceBus\Container\QueryBusFactory::class,
            \Prooph\ProophessorDo\Container\Infrastructure\ReplayBus::class => \Prooph\ProophessorDo\Container\Infrastructure\ReplayBusFactory::class,
            //prooph/event-store-bus-bridge set up
            \Prooph\EventStoreBusBridge\TransactionManager::class => \Prooph\EventStoreBusBridge\Container\TransactionManagerFactory::class,
            \Prooph\EventStoreBusBridge\EventPublisher::class => \Prooph\EventStoreBusBridge\Container\EventPublisherFactory::class,
            \Prooph\Cli\Console\Helper\ClassInfo::class => \Prooph\ProophessorDo\Container\Console\Psr4ClassInfoFactory::class,
        ],
    ],
];

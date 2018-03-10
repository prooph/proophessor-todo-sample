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

return [
    'prooph' => [
        'middleware' => [
            'query' => [
                'response_strategy' => \Prooph\ProophessorDo\Response\JsonResponse::class,
                'message_factory' => \Prooph\Common\Messaging\FQCNMessageFactory::class,
            ],
            'command' => [
                'response_strategy' => \Prooph\ProophessorDo\Response\JsonResponse::class,
                'message_factory' => \Prooph\Common\Messaging\FQCNMessageFactory::class,
            ],
            'event' => [
                'response_strategy' => \Prooph\ProophessorDo\Response\JsonResponse::class,
                'message_factory' => \Prooph\Common\Messaging\FQCNMessageFactory::class,
            ],
            'message' => [
                'response_strategy' => \Prooph\ProophessorDo\Response\JsonResponse::class,
                'message_factory' => \Prooph\Common\Messaging\FQCNMessageFactory::class,
            ],
        ],
        'event_sourcing' => [
            'aggregate_repository' => [
                'todo_list' => [
                    'repository_class' => \Prooph\ProophessorDo\Infrastructure\Repository\EventStoreTodoList::class,
                    'aggregate_type' => \Prooph\ProophessorDo\Model\Todo\Todo::class,
                    'aggregate_translator' => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
                    'stream_name' => 'event_stream',
                ],
                'user_collection' => [
                    'repository_class' => \Prooph\ProophessorDo\Infrastructure\Repository\EventStoreUserCollection::class,
                    'aggregate_type' => \Prooph\ProophessorDo\Model\User\User::class,
                    'aggregate_translator' => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
                    'stream_name' => 'event_stream',
                ],
            ],
        ],
        'event_store' => [
            'default' => [
                'plugins' => [
                    \Prooph\EventStoreBusBridge\EventPublisher::class,
                ],
            ],
        ],
        'service_bus' => [
            'command_bus' => [
                'router' => [
                    'routes' => [
                        \Prooph\ProophessorDo\Model\User\Command\RegisterUser::class => \Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\PostTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\PostTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsDone::class => \Prooph\ProophessorDo\Model\Todo\Handler\MarkTodoAsDoneHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\ReopenTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\ReopenTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\AddDeadlineToTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\AddDeadlineToTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\AddReminderToTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\AddReminderToTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsExpired::class => \Prooph\ProophessorDo\Model\Todo\Handler\MarkTodoAsExpiredHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\NotifyUserOfExpiredTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\NotifyUserOfExpiredTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\RemindTodoAssignee::class => \Prooph\ProophessorDo\Model\Todo\Handler\RemindTodoAssigneeHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\SendTodoReminderMail::class => \Prooph\ProophessorDo\Model\Todo\Handler\SendTodoReminderMailHandler::class,
                    ],
                ],
            ],
            'event_bus' => [
                'plugins' => [
                    \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class,
                ],
                'router' => [
                    'routes' => [
                        \Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded::class => [
                            \Prooph\ProophessorDo\ProcessManager\SendTodoReminderMailProcessManager::class,
                        ],
                        \Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired::class => [
                            \Prooph\ProophessorDo\ProcessManager\SendTodoDeadlineExpiredMailProcessManager::class,
                        ],
                    ],
                ],
            ],
            'query_bus' => [
                'router' => [
                    'routes' => [
                        \Prooph\ProophessorDo\Model\Todo\Query\GetTodoById::class => \Prooph\ProophessorDo\Model\Todo\Handler\GetTodoByIdHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Query\GetTodosByAssigneeId::class => \Prooph\ProophessorDo\Model\Todo\Handler\GetTodosByAssigneeIdHandler::class,
                        \Prooph\ProophessorDo\Model\User\Query\GetAllUsers::class => \Prooph\ProophessorDo\Model\User\Handler\GetAllUsersHandler::class,
                        \Prooph\ProophessorDo\Model\User\Query\GetUserById::class => \Prooph\ProophessorDo\Model\User\Handler\GetUserByIdHandler::class,
                    ],
                ],
            ],
        ],
    ],
];

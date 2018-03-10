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

use Prooph\EventSourcing\Container\Aggregate\AggregateRepositoryFactory;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
            Helper\ServerUrlHelper::class => Helper\ServerUrlHelper::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            Application::class => ApplicationFactory::class,
            Helper\UrlHelper::class => Helper\UrlHelperFactory::class,
            'doctrine.connection.default' => Container\Infrastructure\DoctrineDbalConnectionFactory::class,
            \Zend\Mail\Transport\TransportInterface::class => Container\App\Mail\TransportFactory::class,
            // Action middleware
            App\Action\Home::class => Container\App\Action\HomeFactory::class,
            App\Action\UserList::class => Container\App\Action\UserListFactory::class,
            App\Action\UserRegistration::class => Container\App\Action\UserRegistrationFactory::class,
            App\Action\UserTodoList::class => Container\App\Action\UserTodoListFactory::class,
            App\Action\UserTodoForm::class => Container\App\Action\UserTodoFormFactory::class,
            // Model
            Model\User\Handler\RegisterUserHandler::class => Container\Model\User\RegisterUserHandlerFactory::class,
            Model\User\Service\ChecksUniqueUsersEmailAddress::class => Container\Model\User\ChecksUniqueUsersEmailAddressFactory::class,
            Model\User\UserCollection::class => [AggregateRepositoryFactory::class, 'user_collection'],
            Model\Todo\Handler\PostTodoHandler::class => Container\Model\Todo\PostTodoHandlerFactory::class,
            Model\Todo\Handler\MarkTodoAsDoneHandler::class => Container\Model\Todo\MarkTodoAsDoneHandlerFactory::class,
            Model\Todo\Handler\ReopenTodoHandler::class => Container\Model\Todo\ReopenTodoHandlerFactory::class,
            Model\Todo\Handler\AddDeadlineToTodoHandler::class => Container\Model\Todo\AddDeadlineToTodoHandlerFactory::class,
            Model\Todo\Handler\AddReminderToTodoHandler::class => Container\Model\Todo\AddReminderToTodoHandlerFactory::class,
            Model\Todo\Handler\MarkTodoAsExpiredHandler::class => Container\Model\Todo\MarkTodoAsExpiredHandlerFactory::class,
            Model\Todo\Handler\RemindTodoAssigneeHandler::class => Container\Model\Todo\RemindTodoAssigneeHandlerFactory::class,
            Model\Todo\Handler\SendTodoReminderMailHandler::class => Container\Model\Todo\SendTodoReminderMailHandlerFactory::class,
            Model\Todo\TodoList::class => [AggregateRepositoryFactory::class, 'todo_list'],
            // Projections
            Projection\User\UserFinder::class => Container\Projection\User\UserFinderFactory::class,
            Projection\Todo\TodoFinder::class => Container\Projection\Todo\TodoFinderFactory::class,
            Projection\Todo\TodoReminderFinder::class => Container\Projection\Todo\TodoReminderFinderFactory::class,
            // Subscriber
            ProcessManager\SendTodoReminderMailProcessManager::class => Container\ProcessManager\SendTodoReminderMailSubscriberFactory::class,
            ProcessManager\SendTodoDeadlineExpiredMailProcessManager::class => Container\ProcessManager\SendTodoDeadlineExpiredMailSubscriberFactory::class,
            // Query
            Model\User\Handler\GetAllUsersHandler::class => Container\Model\User\GetAllUsersHandlerFactory::class,
            Model\User\Handler\GetUserByIdHandler::class => Container\Model\User\GetUserByIdHandlerFactory::class,
            Model\Todo\Handler\GetTodoByIdHandler::class => Container\Model\Todo\GetTodoByIdHandlerFactory::class,
            Model\Todo\Handler\GetTodosByAssigneeIdHandler::class => Container\Model\Todo\GetTodosByAssigneeIdHandlerFactory::class,
        ],
    ],
];

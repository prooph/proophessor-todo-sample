<?php
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
            'doctrine.connection.default' => \Prooph\ProophessorDo\Container\Infrastructure\DoctrineDbalConnectionFactory::class,
            // Action middleware
            \Prooph\ProophessorDo\App\Action\Home::class             => \Prooph\ProophessorDo\Container\App\Action\HomeFactory::class,
            \Prooph\ProophessorDo\App\Action\UserList::class         => \Prooph\ProophessorDo\Container\App\Action\UserListFactory::class,
            \Prooph\ProophessorDo\App\Action\UserRegistration::class => \Prooph\ProophessorDo\Container\App\Action\UserRegistrationFactory::class,
            \Prooph\ProophessorDo\App\Action\UserTodoList::class     => \Prooph\ProophessorDo\Container\App\Action\UserTodoListFactory::class,
            \Prooph\ProophessorDo\App\Action\UserTodoForm::class     => \Prooph\ProophessorDo\Container\App\Action\UserTodoFormFactory::class,
            // Model
            \Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler::class => \Prooph\ProophessorDo\Container\Model\User\RegisterUserHandlerFactory::class,
            \Prooph\ProophessorDo\Model\User\UserCollection::class      => \Prooph\ProophessorDo\Container\Infrastructure\Repository\EventStoreUserCollectionFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\PostTodoHandler::class     => \Prooph\ProophessorDo\Container\Model\Todo\PostTodoHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\MarkTodoAsDoneHandler::class     => \Prooph\ProophessorDo\Container\Model\Todo\MarkTodoAsDoneHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\ReopenTodoHandler::class     => \Prooph\ProophessorDo\Container\Model\Todo\ReopenTodoHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\AddDeadlineToTodoHandler::class => \Prooph\ProophessorDo\Container\Model\Todo\AddDeadlineToTodoHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\AddReminderToTodoHandler::class => \Prooph\ProophessorDo\Container\Model\Todo\AddReminderToTodoHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\Handler\RemindTodoAssigneeHandler::class => \Prooph\ProophessorDo\Container\Model\Todo\RemindTodoAssigneeHandlerFactory::class,
            \Prooph\ProophessorDo\Model\Todo\TodoList::class            => \Prooph\ProophessorDo\Container\Infrastructure\Repository\EventStoreTodoListFactory::class,
            // Projections
            \Prooph\ProophessorDo\Projection\User\UserProjector::class => \Prooph\ProophessorDo\Container\Projection\User\UserProjectorFactory::class,
            \Prooph\ProophessorDo\Projection\User\UserFinder::class    => \Prooph\ProophessorDo\Container\Projection\User\UserFinderFactory::class,
            \Prooph\ProophessorDo\Projection\Todo\TodoProjector::class => \Prooph\ProophessorDo\Container\Projection\Todo\TodoProjectorFactory::class,
            \Prooph\ProophessorDo\Projection\Todo\TodoFinder::class    => \Prooph\ProophessorDo\Container\Projection\Todo\TodoFinderFactory::class,
            // Subscriber
            \Prooph\ProophessorDo\App\Mail\SendTodoReminderMailSubscriber::class => \Prooph\ProophessorDo\Container\App\Mail\SendTodoReminderMailSubscriberFactory::class,
        ],
    ],
];

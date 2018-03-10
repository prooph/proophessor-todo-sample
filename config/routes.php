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

/**
 * Expressive routed middleware
 */

/** @var \Zend\Expressive\Application $app */
$app->get('/', \Prooph\ProophessorDo\App\Action\Home::class, 'page::home');
$app->get('/user-list', \Prooph\ProophessorDo\App\Action\UserList::class, 'page::user-list');
$app->get('/user-registration', \Prooph\ProophessorDo\App\Action\UserRegistration::class, 'page::user-registration-form');
$app->get('/user-todo-list/{user_id}', \Prooph\ProophessorDo\App\Action\UserTodoList::class, 'page::user-todo-list')
    ->setOptions([
        'tokens' => [
            'user_id' => '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}',
        ],
    ]);
$app->get('/user-todo-list/{user_id}/new-todo', \Prooph\ProophessorDo\App\Action\UserTodoForm::class, 'page::user-todo-form')
    ->setOptions([
        'tokens' => [
            'user_id' => '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}',
        ],
    ]);
$app->post('/api/commands/register-user', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::register-user')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\User\Command\RegisterUser::class,
        ],
    ]);
$app->post('/api/commands/post-todo', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::post-todo')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\PostTodo::class,
        ],
    ]);
$app->post('/api/commands/mark-todo-as-done', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::mark-todo-as-done')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsDone::class,
        ],
    ]);
$app->post('/api/commands/reopen-todo', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::reopen-todo')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\ReopenTodo::class,
        ],
    ]);
$app->post('/api/commands/add-deadline-to-todo', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::add-deadline-to-todo')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\AddDeadlineToTodo::class,
        ],
    ]);
$app->post('/api/commands/add-reminder-to-todo', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::add-reminder-to-todo')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\AddReminderToTodo::class,
        ],
    ]);
$app->post('/api/commands/mark-todo-as-expired', [
    \Prooph\ProophessorDo\Middleware\JsonPayload::class,
    \Prooph\ProophessorDo\Middleware\JsonError::class,
    \Prooph\Psr7Middleware\CommandMiddleware::class,
], 'command::mark-todo-as-expired')
    ->setOptions([
        'values' => [
            'prooph_command_name' => \Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsExpired::class,
        ],
    ]);

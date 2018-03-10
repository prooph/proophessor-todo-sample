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

use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Prooph\ProophessorDo\Projection\User\UserReadModel;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$container = require 'config/container.php';

$projectionManager = $container->get(ProjectionManager::class);
/* @var ProjectionManager $projectionManager */

$readModel = new UserReadModel($container->get('doctrine.connection.default'));

$projection = $projectionManager->createReadModelProjection('user', $readModel);

$projection
    ->fromStream('event_stream')
    ->when([
        UserWasRegistered::class => function ($state, UserWasRegistered $event) {
            $this->readModel()->stack('insert', [
                'id' => $event->userId()->toString(),
                'name' => $event->name()->toString(),
                'email' => $event->emailAddress()->toString(),
            ]);
        },
        TodoWasPosted::class => function ($state, TodoWasPosted $event) {
            $this->readModel()->stack('postTodo', $event->assigneeId()->toString());
        },
        TodoWasMarkedAsDone::class => function ($state, TodoWasMarkedAsDone $event) {
            $this->readModel()->stack('markTodoAsDone', $event->assigneeId()->toString());
        },
        TodoWasReopened::class => function ($state, TodoWasReopened $event) {
            $this->readModel()->stack('reopenTodo', $event->assigneeId()->toString());
        },
        TodoWasMarkedAsExpired::class => function ($state, TodoWasMarkedAsExpired $event) {
            $this->readModel()->stack('markTodoAsExpired', $event->assigneeId()->toString());
        },
        TodoWasUnmarkedAsExpired::class => function ($state, TodoWasUnmarkedAsExpired $event) {
            $this->readModel()->stack('unmarkTodoAsExpired', $event->assigneeId()->toString());
        },
    ])
    ->run();

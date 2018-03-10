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
use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Projection\Todo\TodoReadModel;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$container = require 'config/container.php';

$projectionManager = $container->get(ProjectionManager::class);
/* @var ProjectionManager $projectionManager */

$readModel = new TodoReadModel($container->get('doctrine.connection.default'));

$projection = $projectionManager->createReadModelProjection('todo', $readModel);

$projection
    ->fromStream('event_stream')
    ->when([
        TodoWasPosted::class => function ($state, TodoWasPosted $event) {
            $this->readModel()->stack('insert', [
                'id' => $event->todoId()->toString(),
                'assignee_id' => $event->assigneeId()->toString(),
                'text' => $event->text()->toString(),
                'status' => mb_strtolower($event->todoStatus()->toString()),
            ]);
        },
        TodoWasMarkedAsDone::class => function ($state, TodoWasMarkedAsDone $event) {
            $this->readModel()->stack(
                'update',
                [
                    'status' => mb_strtolower($event->newStatus()->toString()),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
        TodoWasReopened::class => function ($state, TodoWasReopened $event) {
            $this->readModel()->stack(
                'update',
                [
                    'status' => mb_strtolower($event->status()->toString()),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
        DeadlineWasAddedToTodo::class => function ($state, DeadlineWasAddedToTodo $event) {
            $this->readModel()->stack(
                'update',
                [
                    'deadline' => $event->deadline()->toString(),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
        ReminderWasAddedToTodo::class => function ($state, ReminderWasAddedToTodo $event) {
            $this->readModel()->stack(
                'update',
                [
                    'reminder' => $event->reminder()->toString(),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
        TodoWasMarkedAsExpired::class => function ($state, TodoWasMarkedAsExpired $event) {
            $this->readModel()->stack(
                'update',
                [
                    'status' => mb_strtolower($event->newStatus()->toString()),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
        TodoWasUnmarkedAsExpired::class => function ($state, TodoWasUnmarkedAsExpired $event) {
            $this->readModel()->stack(
                'update',
                [
                    'status' => mb_strtolower($event->newStatus()->toString()),
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]
            );
        },
    ])
    ->run();

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
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Projection\Todo\TodoReminderReadModel;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$container = require 'config/container.php';

$projectionManager = $container->get(ProjectionManager::class);
/* @var ProjectionManager $projectionManager */

$readModel = new TodoReminderReadModel($container->get('doctrine.connection.default'));

$projection = $projectionManager->createReadModelProjection('todo_reminder', $readModel);

$projection
    ->fromStream('event_stream')
    ->when([
        ReminderWasAddedToTodo::class => function ($state, ReminderWasAddedToTodo $event) {
            $this->readModel()->stack('remove', [
                'todo_id' => $event->todoId()->toString(),
            ]);

            $reminder = $event->reminder();

            $this->readModel()->stack('insert', [
                'todo_id' => $event->todoId()->toString(),
                'reminder' => $reminder->toString(),
                'status' => $reminder->status()->toString(),
            ]);
        },
        TodoAssigneeWasReminded::class => function ($state, TodoAssigneeWasReminded $event) {
            $this->readModel()->stack(
                'update',
                [
                    'status' => $event->reminder()->status()->toString(),
                ],
                [
                    'todo_id' => $event->todoId()->toString(),
                ]
            );
        },
    ])
    ->run();

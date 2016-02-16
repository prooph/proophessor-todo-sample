<?php
/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 5/4/15 - 5:44 PM
 */
namespace Prooph\ProophessorDo\Projection\Todo;

use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Projection\Table;
use Doctrine\DBAL\Connection;

/**
 * Class TodoProjector
 *
 * @package Prooph\ProophessorDo\Projection\Todo
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class TodoProjector
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var TodoFinder
     */
    private $todoFinder;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection, TodoFinder $todoFinder)
    {
        $this->connection = $connection;
        $this->todoFinder = $todoFinder;
    }

    /**
     * @param TodoWasPosted $event
     */
    public function onTodoWasPosted(TodoWasPosted $event)
    {
        $user = $this->todoFinder->findById($event->todoId()->toString());

        if (!$user) {
            $this->connection->insert(
                Table::TODO,
                [
                    'id'          => $event->todoId()->toString(),
                    'assignee_id' => $event->assigneeId()->toString(),
                    'text'        => $event->text(),
                    'status'      => $event->todoStatus()->toString()
                ]);
        } else {
            $this->connection->update(
                Table::TODO,
                [
                    'assignee_id' => $event->assigneeId()->toString(),
                    'text'        => $event->text(),
                    'status'      => $event->todoStatus()->toString()
                ],
                [
                    'id' => $event->todoId()->toString(),
                ]);
        }
    }

    /**
     * @param TodoWasMarkedAsDone $event
     */
    public function onTodoWasMarkedAsDone(TodoWasMarkedAsDone $event)
    {
        $this->connection->update(Table::TODO,
            [
                'status' => $event->newStatus()->toString()
            ],
            [
                'id' => $event->todoId()->toString()
            ]
        );
    }

    /**
     * @param DeadlineWasAddedToTodo $event
     * @return void
     */
    public function onDeadlineWasAddedToTodo(DeadlineWasAddedToTodo $event)
    {
        $this->connection->update(
            Table::TODO,
            [
                'deadline' => $event->deadline()->toString(),
            ],
            [
                'id' => $event->todoId()->toString(),
            ]
        );
    }
}

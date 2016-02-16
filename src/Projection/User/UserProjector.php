<?php
/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 5/2/15 - 11:44 PM
 */
namespace Prooph\ProophessorDo\Projection\User;

use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Prooph\ProophessorDo\Projection\Table;
use Doctrine\DBAL\Connection;

/**
 * Class UserProjector
 *
 * @package Prooph\ProophessorDo\Projection\User
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class UserProjector
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var UserFinder
     */
    private $userFinder;

    /**
     * @param Connection $connection
     * @param UserFinder $userFinder
     */
    public function __construct(Connection $connection, UserFinder $userFinder)
    {
        $this->connection = $connection;
        $this->userFinder = $userFinder;
    }

    /**
     * @param UserWasRegistered $event
     */
    public function onUserWasRegistered(UserWasRegistered $event)
    {
        $user = $this->userFinder->findById($event->userId()->toString());

        if (!$user) {
            $this->connection->insert(
                Table::USER,
                [
                    'id'    => $event->userId()->toString(),
                    'name'  => $event->name(),
                    'email' => $event->emailAddress()->toString()
                ]
            );
        } else {
            $this->connection->update(
                Table::USER,
                [
                    'name'  => $event->name(),
                    'email' => $event->emailAddress()->toString()
                ],
                [
                    'id' => $event->userId()->toString(),
                ]);
        }
    }

    /**
     * Increases the open_todos counter of the assignee by one
     *
     * @param TodoWasPosted $event
     * @throws \RuntimeException
     */
    public function onTodoWasPosted(TodoWasPosted $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new \RuntimeException(
                sprintf(
                    "Data of the assigned user of the todo %s cannot be found",
                    $event->todoId()->toString()
                )
            );
        }

        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $event->assigneeId()->toString());

        $stmt->execute();
    }

    /**
     * @param TodoWasMarkedAsDone $event
     * @throws \RuntimeException if data of the the assigned user can not be found
     */
    public function onTodoWasMarkedAsDone(TodoWasMarkedAsDone $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new \RuntimeException(
                sprintf(
                    "Data of the assigned user of the todo %s cannot be found",
                    $event->todoId()->toString()
                )
            );
        }

        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos - 1, done_todos = done_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $user->id);

        $stmt->execute();
    }
}

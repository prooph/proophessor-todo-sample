<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prooph\ProophessorDo\Projection\User;

use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Prooph\ProophessorDo\Model\User\Exception\UserNotFound;
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
     * @throws UserNotFound
     */
    public function onTodoWasPosted(TodoWasPosted $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new UserNotFound(
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
     * @throws UserNotFound if data of the the assigned user can not be found
     */
    public function onTodoWasMarkedAsDone(TodoWasMarkedAsDone $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new UserNotFound(
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

    /**
     * @param TodoWasReopened $event
     * @throws UserNotFound if data of the the assigned user can not be found
     */
    public function onTodoWasReopened(TodoWasReopened $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new UserNotFound(
                sprintf(
                    "Data of the assigned user of the todo %s cannot be found",
                    $event->todoId()->toString()
                )
            );
        }

        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1, done_todos = done_todos - 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $user->id);

        $stmt->execute();
    }

    /**
     * @param TodoWasMarkedAsExpired $event
     * @throws UserNotFound if data of the the assigned user can not be found
     */
    public function onTodoWasMarkedAsExpired(TodoWasMarkedAsExpired $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new UserNotFound(
                sprintf(
                    "Data of the assigned user of the todo %s cannot be found",
                    $event->todoId()->toString()
                )
            );
        }

        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos - 1, expired_todos = expired_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $user->id);

        $stmt->execute();
    }

    /**
     * @param TodoWasUnmarkedAsExpired $event
     * @throws UserNotFound if data of the the assigned user can not be found
     */
    public function onTodoWasUnmarkedAsExpired(TodoWasUnmarkedAsExpired $event)
    {
        $user = $this->userFinder->findUserOfTodo($event->todoId()->toString());

        if (! $user) {
            throw new UserNotFound(
                sprintf(
                    "Data of the assigned user of the todo %s cannot be found",
                    $event->todoId()->toString()
                )
            );
        }

        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1, expired_todos = expired_todos - 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $user->id);

        $stmt->execute();
    }
}

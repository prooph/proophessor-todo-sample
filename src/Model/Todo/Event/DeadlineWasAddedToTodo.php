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

namespace Prooph\ProophessorDo\Model\Todo\Event;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\User\UserId;

final class DeadlineWasAddedToTodo extends AggregateChanged
{
    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var TodoDeadline
     */
    private $deadline;

    public static function byUserToDate(TodoId $todoId, UserId $userId, TodoDeadline $deadline): DeadlineWasAddedToTodo
    {
        /** @var self $event */
        $event = self::occur($todoId->toString(), [
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'deadline' => $deadline->toString(),
        ]);

        $event->todoId = $todoId;
        $event->userId = $userId;
        $event->deadline = $deadline;

        return $event;
    }

    public function todoId(): TodoId
    {
        if (! $this->todoId) {
            $this->todoId = TodoId::fromString($this->payload['todo_id']);
        }

        return $this->todoId;
    }

    public function userId(): UserId
    {
        if (! $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function deadline(): TodoDeadline
    {
        if (! $this->deadline) {
            $this->deadline = TodoDeadline::fromString($this->payload['deadline']);
        }

        return $this->deadline;
    }
}

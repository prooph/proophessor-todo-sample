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
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;
use Prooph\ProophessorDo\Model\User\UserId;

final class TodoWasMarkedAsDone extends AggregateChanged
{
    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var TodoStatus
     */
    private $oldStatus;

    /**
     * @var TodoStatus
     */
    private $newStatus;

    /**
     * @var UserId
     */
    private $assigneeId;

    public static function fromStatus(TodoId $todoId, TodoStatus $oldStatus, TodoStatus $newStatus, UserId $assigneeId): TodoWasMarkedAsDone
    {
        /** @var self $event */
        $event = self::occur($todoId->toString(), [
            'old_status' => $oldStatus->toString(),
            'new_status' => $newStatus->toString(),
            'assignee_id' => $assigneeId->toString(),
        ]);

        $event->todoId = $todoId;
        $event->oldStatus = $oldStatus;
        $event->newStatus = $newStatus;
        $event->assigneeId = $assigneeId;

        return $event;
    }

    public function todoId(): TodoId
    {
        if (null === $this->todoId) {
            $this->todoId = TodoId::fromString($this->aggregateId());
        }

        return $this->todoId;
    }

    public function oldStatus(): TodoStatus
    {
        if (null === $this->oldStatus) {
            $this->oldStatus = TodoStatus::byName($this->payload['old_status']);
        }

        return $this->oldStatus;
    }

    public function newStatus(): TodoStatus
    {
        if (null === $this->newStatus) {
            $this->newStatus = TodoStatus::byName($this->payload['new_status']);
        }

        return $this->newStatus;
    }

    public function assigneeId(): UserId
    {
        if (null === $this->assigneeId) {
            $this->assigneeId = UserId::fromString($this->payload['assignee_id']);
        }

        return $this->assigneeId;
    }
}

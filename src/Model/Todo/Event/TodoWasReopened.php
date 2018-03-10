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

final class TodoWasReopened extends AggregateChanged
{
    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var TodoStatus
     */
    private $status;

    /**
     * @var UserId
     */
    private $assigneeId;

    public static function withStatus(TodoId $todoId, TodoStatus $status, UserId $assigneeId): TodoWasReopened
    {
        /** @var self $event */
        $event = self::occur($todoId->toString(), [
            'status' => $status->toString(),
            'assignee_id' => $assigneeId->toString(),
        ]);

        $event->todoId = $todoId;
        $event->status = $status;
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

    public function status(): TodoStatus
    {
        if (null === $this->status) {
            $this->status = TodoStatus::byName($this->payload['status']);
        }

        return $this->status;
    }

    public function assigneeId(): UserId
    {
        if (null === $this->assigneeId) {
            $this->assigneeId = UserId::fromString($this->payload['assignee_id']);
        }

        return $this->assigneeId;
    }
}

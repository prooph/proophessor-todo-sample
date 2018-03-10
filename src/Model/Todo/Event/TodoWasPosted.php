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
use Prooph\ProophessorDo\Model\Todo\TodoText;
use Prooph\ProophessorDo\Model\User\UserId;

final class TodoWasPosted extends AggregateChanged
{
    /**
     * @var UserId
     */
    private $assigneeId;

    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var TodoStatus
     */
    private $todoStatus;

    public static function byUser(UserId $assigneeId, TodoText $text, TodoId $todoId, TodoStatus $todoStatus): TodoWasPosted
    {
        /** @var self $event */
        $event = self::occur($todoId->toString(), [
            'assignee_id' => $assigneeId->toString(),
            'text' => $text->toString(),
            'status' => $todoStatus->toString(),
        ]);

        $event->todoId = $todoId;
        $event->assigneeId = $assigneeId;
        $event->todoStatus = $todoStatus;

        return $event;
    }

    public function todoId(): TodoId
    {
        if (null === $this->todoId) {
            $this->todoId = TodoId::fromString($this->aggregateId());
        }

        return $this->todoId;
    }

    public function assigneeId(): UserId
    {
        if (null === $this->assigneeId) {
            $this->assigneeId = UserId::fromString($this->payload['assignee_id']);
        }

        return $this->assigneeId;
    }

    public function text(): TodoText
    {
        return TodoText::fromString($this->payload['text']);
    }

    public function todoStatus(): TodoStatus
    {
        if (null === $this->todoStatus) {
            $this->todoStatus = TodoStatus::byName($this->payload['status']);
        }

        return $this->todoStatus;
    }
}

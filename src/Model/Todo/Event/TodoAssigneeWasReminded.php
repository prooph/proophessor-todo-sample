<?php

namespace Prooph\ProophessorDo\Model\Todo\Event;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\User\UserId;

/**
 * Class AssigneeWasNotified
 *
 * @package Prooph\ProophessorDo\Model\Todo\Event
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
final class TodoAssigneeWasReminded extends AggregateChanged
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
     * @param TodoId $todoId
     * @param UserId $userId
     * @return DeadlineWasAddedToTodo
     */
    public static function forAssignee(TodoId $todoId, UserId $userId)
    {
        $event = self::occur($todoId->toString(), [
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
        ]);

        $event->todoId = $todoId;
        $event->userId = $userId;

        return $event;
    }

    /**
     * @return UserId
     */
    public function todoId()
    {
        if (!$this->todoId) {
            $this->todoId = TodoId::fromString($this->payload['todo_id']);
        }

        return $this->todoId;
    }

    /**
     * @return UserId
     */
    public function userId()
    {
        if (!$this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}

<?php
namespace Prooph\ProophessorDo\Model\Todo\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Prooph\ProophessorDo\Model\Todo\TodoId;

/**
 * Class RemindTodoAssignee
 *
 * @package Prooph\ProophessorDo\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
final class RemindTodoAssignee extends Command implements PayloadConstructable
{
    use PayloadTrait;

    /**
     *
     * @param TodoId $todoId
     * @return RemindTodoAssignee
     */
    public static function forTodo(TodoId $todoId)
    {
        return new self([
            'todo_id' => $todoId->toString(),
        ]);
    }


    /**
     * @return TodoId
     */
    public function todoId()
    {
        return TodoId::fromString($this->payload['todo_id']);
    }
}

<?php
namespace Prooph\ProophessorDo\Model\Todo\Handler;

use Prooph\ProophessorDo\Model\Todo\Command\RemindTodoAssignee;
use Prooph\ProophessorDo\Model\Todo\Exception\TodoNotFound;
use Prooph\ProophessorDo\Model\Todo\TodoList;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;

/**
 * Class RemindTodoAssigneeHandler
 *
 * @package Prooph\ProophessorDo\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
final class RemindTodoAssigneeHandler
{
    /**
     * @var TodoList
     */
    private $todoList;
    /**
     * @var TodoFinder
     */
    private $todoFinder;

    /**
     * @param TodoList $todoList
     * @param TodoFinder $todoFinder
     */
    public function __construct(TodoList $todoList, TodoFinder $todoFinder)
    {
        $this->todoList = $todoList;
        $this->todoFinder = $todoFinder;
    }

    /**
     * @param RemindTodoAssignee $command
     */
    public function __invoke(RemindTodoAssignee $command)
    {
        $todo = $this->todoList->get($command->todoId());
        if (!$todo) {
            throw TodoNotFound::withTodoId($command->todoId());
        }

        $todoData = $this->todoFinder->findById($command->todoId()->toString());

        if ($todoData->reminded) {
            var_dump('do nothing, assignee already reminded');

            return;
        }

        $reminder = new \DateTimeImmutable($todoData->reminder, new \DateTimeZone('UTC'));
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        if ($reminder > $now) {
            var_dump('do nothing, reminder in future');

            return;
        }

        $todo->remindAssignee();
    }
}

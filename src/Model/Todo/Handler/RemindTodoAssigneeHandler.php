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

namespace Prooph\ProophessorDo\Model\Todo\Handler;

use Prooph\ProophessorDo\Model\Todo\Command\RemindTodoAssignee;
use Prooph\ProophessorDo\Model\Todo\Exception\TodoNotFound;
use Prooph\ProophessorDo\Model\Todo\Todo;
use Prooph\ProophessorDo\Model\Todo\TodoList;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;

class RemindTodoAssigneeHandler
{
    /**
     * @var TodoList
     */
    private $todoList;

    public function __construct(TodoList $todoList)
    {
        $this->todoList = $todoList;
    }

    public function __invoke(RemindTodoAssignee $command): void
    {
        $todo = $this->todoList->get($command->todoId());

        if (! $todo) {
            throw TodoNotFound::withTodoId($command->todoId());
        }

        $reminder = $todo->reminder();

        if ($this->reminderShouldBeProcessed($todo, $reminder)) {
            $todo->remindAssignee($reminder);

            $this->todoList->save($todo);
        }
    }

    private function reminderShouldBeProcessed(Todo $todo, TodoReminder $reminder): bool
    {
        // drop command, wrong reminder
        if (! $todo->reminder()->sameValueAs($reminder)) {
            return false;
        }

        // drop command, reminder is closed
        if (! $reminder->isOpen()) {
            return false;
        }

        // drop command, reminder is in future
        if ($reminder->isInTheFuture()) {
            return false;
        }

        return true;
    }
}

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

namespace Prooph\ProophessorDo\Model\Todo;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\ProophessorDo\Model\Entity;
use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Model\User\UserId;

final class Todo extends AggregateRoot implements Entity
{
    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var UserId
     */
    private $assigneeId;

    /**
     * @var TodoText
     */
    private $text;

    /**
     * @var TodoStatus
     */
    private $status;

    /**
     * @var null|TodoDeadline
     */
    private $deadline;

    /**
     * @var TodoReminder
     */
    private $reminder;

    /**
     * @var bool
     */
    private $reminded = false;

    public static function post(TodoText $text, UserId $assigneeId, TodoId $todoId): Todo
    {
        $self = new self();
        $self->recordThat(TodoWasPosted::byUser($assigneeId, $text, $todoId, TodoStatus::OPEN()));

        return $self;
    }

    /**
     * @throws Exception\TodoNotOpen
     */
    public function markAsDone(): void
    {
        $status = TodoStatus::DONE();

        if (! $this->status->is(TodoStatus::OPEN())) {
            throw Exception\TodoNotOpen::triedStatus($status, $this);
        }

        $this->recordThat(TodoWasMarkedAsDone::fromStatus($this->todoId, $this->status, $status, $this->assigneeId));
    }

    /**
     * @throws Exception\InvalidDeadline
     * @throws Exception\TodoNotOpen
     */
    public function addDeadline(UserId $userId, TodoDeadline $deadline): void
    {
        if (! $this->assigneeId()->sameValueAs($userId)) {
            throw Exception\InvalidDeadline::userIsNotAssignee($userId, $this->assigneeId());
        }

        if ($deadline->isInThePast()) {
            throw Exception\InvalidDeadline::deadlineInThePast($deadline);
        }

        if ($this->status->is(TodoStatus::DONE())) {
            throw Exception\TodoNotOpen::triedToAddDeadline($deadline, $this->status);
        }

        $this->recordThat(DeadlineWasAddedToTodo::byUserToDate($this->todoId, $this->assigneeId, $deadline));

        if ($this->isMarkedAsExpired()) {
            $this->unmarkAsExpired();
        }
    }

    /**
     * @throws Exception\TodoNotExpired
     * @throws Exception\TodoNotOpen
     */
    public function markAsExpired(): void
    {
        $status = TodoStatus::EXPIRED();

        if (! $this->status->is(TodoStatus::OPEN()) || $this->status->is(TodoStatus::EXPIRED())) {
            throw Exception\TodoNotOpen::triedToExpire($this->status);
        }

        if ($this->deadline->isMet()) {
            throw Exception\TodoNotExpired::withDeadline($this->deadline, $this);
        }

        $this->recordThat(TodoWasMarkedAsExpired::fromStatus($this->todoId, $this->status, $status, $this->assigneeId));
    }

    /**
     * @throws Exception\TodoNotExpired
     */
    public function unmarkAsExpired(): void
    {
        $status = TodoStatus::OPEN();

        if (! $this->isMarkedAsExpired()) {
            throw Exception\TodoNotExpired::withDeadline($this->deadline, $this);
        }

        $this->recordThat(TodoWasUnmarkedAsExpired::fromStatus($this->todoId, $this->status, $status, $this->assigneeId));
    }

    private function isMarkedAsExpired(): bool
    {
        return $this->status->is(TodoStatus::EXPIRED());
    }

    /**
     * @throws Exception\InvalidReminder
     */
    public function addReminder(UserId $userId, TodoReminder $reminder): void
    {
        if (! $this->assigneeId()->sameValueAs($userId)) {
            throw Exception\InvalidReminder::userIsNotAssignee($userId, $this->assigneeId());
        }

        if ($reminder->isInThePast()) {
            throw Exception\InvalidReminder::reminderInThePast($reminder);
        }

        if ($this->status->is(TodoStatus::DONE())) {
            throw Exception\TodoNotOpen::triedToAddReminder($reminder, $this->status);
        }

        $this->recordThat(ReminderWasAddedToTodo::byUserToDate($this->todoId, $this->assigneeId, $reminder));
    }

    /**
     * @throws Exception\InvalidReminder
     */
    public function remindAssignee(TodoReminder $reminder): void
    {
        if ($this->status->is(TodoStatus::DONE())) {
            throw Exception\TodoNotOpen::triedToAddReminder($reminder, $this->status);
        }

        if (! $this->reminder->sameValueAs($reminder)) {
            throw Exception\InvalidReminder::reminderNotCurrent($this->reminder, $reminder);
        }

        if (! $this->reminder->isOpen()) {
            throw Exception\InvalidReminder::alreadyReminded();
        }

        if ($reminder->isInTheFuture()) {
            throw Exception\InvalidReminder::reminderInTheFuture($reminder);
        }

        $this->recordThat(TodoAssigneeWasReminded::forAssignee($this->todoId, $this->assigneeId, $reminder->close()));
    }

    public function reopenTodo(): void
    {
        if (! $this->status->is(TodoStatus::DONE())) {
            throw Exception\CannotReopenTodo::notMarkedDone($this);
        }

        $this->recordThat(TodoWasReopened::withStatus($this->todoId, TodoStatus::OPEN(), $this->assigneeId));
    }

    public function deadline(): ?TodoDeadline
    {
        return $this->deadline;
    }

    public function reminder(): ?TodoReminder
    {
        return $this->reminder;
    }

    public function assigneeWasReminded(): bool
    {
        return $this->reminded;
    }

    public function todoId(): TodoId
    {
        return $this->todoId;
    }

    public function text(): TodoText
    {
        return $this->text;
    }

    public function assigneeId(): UserId
    {
        return $this->assigneeId;
    }

    public function status(): TodoStatus
    {
        return $this->status;
    }

    protected function whenTodoWasPosted(TodoWasPosted $event): void
    {
        $this->todoId = $event->todoId();
        $this->assigneeId = $event->assigneeId();
        $this->text = $event->text();
        $this->status = $event->todoStatus();
    }

    protected function whenTodoWasMarkedAsDone(TodoWasMarkedAsDone $event): void
    {
        $this->status = $event->newStatus();
    }

    /**
     * @param TodoWasMarkedAsExpired $event
     */
    protected function whenTodoWasMarkedAsExpired(TodoWasMarkedAsExpired $event)
    {
        $this->status = $event->newStatus();
    }

    protected function whenTodoWasUnmarkedAsExpired(TodoWasUnmarkedAsExpired $event): void
    {
        $this->status = $event->newStatus();
    }

    protected function whenTodoWasReopened(TodoWasReopened $event): void
    {
        $this->status = $event->status();
    }

    protected function whenDeadlineWasAddedToTodo(DeadlineWasAddedToTodo $event): void
    {
        $this->deadline = $event->deadline();
    }

    protected function whenReminderWasAddedToTodo(ReminderWasAddedToTodo $event): void
    {
        $this->reminder = $event->reminder();
    }

    protected function whenTodoAssigneeWasReminded(TodoAssigneeWasReminded $event): void
    {
        $this->reminder = $event->reminder();
    }

    protected function aggregateId(): string
    {
        return $this->todoId->toString();
    }

    public function sameIdentityAs(Entity $other): bool
    {
        return get_class($this) === get_class($other) && $this->todoId->sameValueAs($other->todoId);
    }

    /**
     * Apply given event
     */
    protected function apply(AggregateChanged $e): void
    {
        $handler = $this->determineEventHandlerMethodFor($e);

        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }

        $this->{$handler}($e);
    }

    protected function determineEventHandlerMethodFor(AggregateChanged $e): string
    {
        return 'when' . implode(array_slice(explode('\\', get_class($e)), -1));
    }
}

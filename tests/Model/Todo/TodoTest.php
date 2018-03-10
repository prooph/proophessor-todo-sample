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

namespace ProophTest\ProophessorDo\Model\Todo;

use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Exception\InvalidDeadline;
use Prooph\ProophessorDo\Model\Todo\Exception\InvalidReminder;
use Prooph\ProophessorDo\Model\Todo\Exception\TodoNotExpired;
use Prooph\ProophessorDo\Model\Todo\Exception\TodoNotOpen;
use Prooph\ProophessorDo\Model\Todo\Todo;
use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use Prooph\ProophessorDo\Model\Todo\TodoReminderStatus;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;
use Prooph\ProophessorDo\Model\Todo\TodoText;
use Prooph\ProophessorDo\Model\User\UserId;
use ProophTest\ProophessorDo\TestCase;
use Ramsey\Uuid\Uuid;

class TodoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_a_new_todo_when_posting_a_text(): void
    {
        $assigneeId = UserId::generate();
        $todoId = TodoId::generate();
        $text = TodoText::fromString('This is test todo');

        $todo = Todo::post($text, $assigneeId, $todoId);

        $this->assertInstanceOf(Todo::class, $todo);

        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(1, count($events));

        $this->assertInstanceOf(TodoWasPosted::class, $events[0]);

        $expectedPayload = [
            'text' => $text->toString(),
            'assignee_id' => $assigneeId->toString(),
            'status' => 'OPEN',
        ];

        $this->assertEquals($expectedPayload, $events[0]->payload());
    }

    /**
     * @test
     */
    public function it_marks_an_open_todo_as_done(): Todo
    {
        $todoId = TodoId::generate();
        $text = TodoText::fromString('This is an unit test todo');
        $todo = Todo::post($text, UserId::generate(), $todoId);
        $todo->markAsDone();

        $this->assertTrue($todo->status()->is(TodoStatus::DONE()));

        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(2, count($events));

        $this->assertInstanceOf(TodoWasMarkedAsDone::class, $events[1]);

        $payload = $events[1]->payload();

        $this->assertArrayHasKey('old_status', $payload);
        $this->assertEquals('OPEN', $payload['old_status']);
        $this->assertArrayHasKey('new_status', $payload);
        $this->assertEquals('DONE', $payload['new_status']);
        $this->assertArrayHasKey('assignee_id', $payload);
        $this->assertTrue(Uuid::isValid($payload['assignee_id']));

        return $todo;
    }

    /**
     * @test
     * @depends it_marks_an_open_todo_as_done
     */
    public function it_throws_an_exception_when_marking_a_todo_already_done_as_done(Todo $todo): void
    {
        $this->expectException(TodoNotOpen::class);
        $todo->markAsDone();
    }

    /**
     * @test
     */
    public function it_adds_a_deadline_to_todo(): Todo
    {
        $todoId = TodoId::generate();
        $userId = UserId::generate();
        $deadline = TodoDeadline::fromString('2047-12-31 12:00:00');
        $text = TodoText::fromString('Do something tomorrow');
        $todo = Todo::post($text, $userId, $todoId);

        $this->assertNull($todo->deadline());

        $todo->addDeadline($userId, $deadline);
        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(2, count($events));

        $this->assertInstanceOf(DeadlineWasAddedToTodo::class, $events[1]);

        $expectedPayload = [
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'deadline' => $deadline->toString(),
        ];

        $this->assertEquals($expectedPayload, $events[1]->payload());

        return $todo;
    }

    /**
     * @test
     * @depends it_adds_a_deadline_to_todo
     */
    public function it_can_add_another_deadline_if_desired(Todo $todo): Todo
    {
        $todo->addDeadline(
            $todo->assigneeId(),
            TodoDeadline::fromString('2047-12-11 12:00:00')
        );
        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(1, count($events));
        $this->assertInstanceOf(DeadlineWasAddedToTodo::class, $events[0]);

        return $todo;
    }

    /**
     * @test
     * @depends it_adds_a_deadline_to_todo
     */
    public function it_throws_an_exception_if_deadline_is_in_the_past(Todo $todo): void
    {
        $this->expectException(InvalidDeadline::class);

        $todo->addDeadline(
            $todo->assigneeId(),
            TodoDeadline::fromString('1980-12-11 12:00:00')
        );
    }

    /**
     * @test
     * @depends it_adds_a_deadline_to_todo
     */
    public function it_throws_an_exception_if_user_is_not_the_assignee(Todo $todo): void
    {
        $this->expectException(InvalidDeadline::class);

        $todo->addDeadline(
            UserId::generate(),
            TodoDeadline::fromString('2047-12-11 12:00:00')
        );
    }

    /**
     * @test
     * @depends it_adds_a_deadline_to_todo
     */
    public function it_throws_an_exception_if_todo_is_closed(Todo $todo): void
    {
        $this->expectException(TodoNotOpen::class);

        $todo->markAsDone();
        $todo->addDeadline(
            $todo->assigneeId(),
            TodoDeadline::fromString('2047-12-11 12:00:00')
        );
    }

    /**
     * @test
     */
    public function it_adds_a_reminder_to_todo(): Todo
    {
        $todoId = TodoId::generate();
        $userId = UserId::generate();
        $reminder = TodoReminder::from('2047-12-31 12:00:00', TodoReminderStatus::OPEN()->getName());
        $text = TodoText::fromString('Do something tomorrow');
        $todo = Todo::post($text, $userId, $todoId);
        $this->popRecordedEvent($todo);

        $this->assertNull($todo->reminder());

        $todo->addReminder($userId, $reminder);
        $events = $this->popRecordedEvent($todo);

        $this->assertCount(1, $events);

        $this->assertInstanceOf(ReminderWasAddedToTodo::class, $events[0]);

        $expectedPayload = [
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'reminder' => $reminder->toString(),
        ];

        $this->assertEquals($expectedPayload, $events[0]->payload());

        return $todo;
    }

    /**
     * @test
     * @depends it_adds_a_reminder_to_todo
     */
    public function it_can_add_another_reminder_if_desired(Todo $todo): Todo
    {
        $todo->addReminder(
            $todo->assigneeId(), TodoReminder::from('2047-12-11 12:00:00', TodoReminderStatus::OPEN()->getName())
        );
        $events = $this->popRecordedEvent($todo);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ReminderWasAddedToTodo::class, $events[0]);

        return $todo;
    }

    /**
     * @test
     * @depends it_adds_a_reminder_to_todo
     */
    public function it_throws_an_exception_if_reminder_is_in_the_past(Todo $todo): void
    {
        $this->expectException(InvalidReminder::class);

        $todo->addReminder(
            $todo->assigneeId(), TodoReminder::from('1980-12-11 12:00:00', TodoReminderStatus::OPEN()->getName())
        );
    }

    /**
     * @test
     * @depends it_adds_a_reminder_to_todo
     */
    public function it_throws_an_exception_if_user_who_adds_reminder_is_not_the_assignee(Todo $todo): void
    {
        $this->expectException(InvalidReminder::class);

        $todo->addReminder(
            UserId::generate(), TodoReminder::from('2047-12-11 12:00:00', TodoReminderStatus::OPEN()->getName())
        );
    }

    /**
     * @test
     * @depends it_adds_a_reminder_to_todo
     */
    public function it_throws_an_exception_if_todo_is_closed_while_setting_a_reminder(Todo $todo): void
    {
        $todo->markAsDone();

        $this->expectException(TodoNotOpen::class);

        $todo->addReminder(
            $todo->assigneeId(), TodoReminder::from('2047-12-11 12:00:00', TodoReminderStatus::OPEN()->getName())
        );
    }

    /**
     * @test
     */
    public function it_can_remind_the_assignee(): Todo
    {
        $todo = $this->todoWithReminderInThePast();
        $todo->remindAssignee(TodoReminder::from('2000-12-11 12:00:00', TodoReminderStatus::OPEN()->getName()));

        $events = $this->popRecordedEvent($todo);

        $this->assertCount(1, $events);
        $this->assertInstanceOf(TodoAssigneeWasReminded::class, $events[0]);

        return $todo;
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_todo_is_closed_while_reminding_the_assignee(): void
    {
        $this->expectException(TodoNotOpen::class);

        $todo = $this->todoWithReminderInThePast();

        $todo->markAsDone();

        $todo->remindAssignee(TodoReminder::from('2000-12-11 12:00:00', TodoReminderStatus::OPEN()->getName()));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_assignee_should_be_notified_about_a_reminder_which_is_not_the_current_on(): void
    {
        $this->expectException(InvalidReminder::class);

        $todo = $this->todoWithReminderInThePast();

        $todo->remindAssignee(TodoReminder::from('2046-12-11 12:00:00', TodoReminderStatus::OPEN()->getName()));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_assignee_should_be_notified_about_a_reminder_in_the_future(): void
    {
        $todo = $this->todoWithReminderInThePast();

        $reminder = TodoReminder::from('2046-12-11 12:00:00', TodoReminderStatus::OPEN()->getName());
        $todo->addReminder($todo->assigneeId(), $reminder);

        $this->expectException(InvalidReminder::class);
        $todo->remindAssignee($reminder);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_assignee_should_be_notified_about_a_closed_reminder(): void
    {
        $todo = $this->todoWithReminderInThePast();

        $todo->remindAssignee(TodoReminder::from('2000-12-11 12:00:00', TodoReminderStatus::OPEN()->getName()));

        $this->expectException(InvalidReminder::class);
        $todo->remindAssignee(TodoReminder::from('2000-12-11 12:00:00', TodoReminderStatus::OPEN()->getName()));
    }

    private function todoWithReminderInThePast(): Todo
    {
        $userId = UserId::generate();
        $todoId = TodoId::generate();
        $reminder = TodoReminder::from('2000-12-11 12:00:00', TodoReminderStatus::OPEN()->getName());
        $text = TodoText::fromString('test');

        $events = [
            TodoWasPosted::byUser($userId, $text, $todoId, TodoStatus::OPEN()),
            ReminderWasAddedToTodo::byUserToDate($todoId, $userId, $reminder),
        ];

        return $this->reconstituteAggregateFromHistory(Todo::class, $events);
    }

    /**
     * @test
     */
    public function it_marks_an_open_todo_as_expired(): Todo
    {
        $todoId = TodoId::generate();
        $userId = UserId::generate();
        $deadline = TodoDeadline::fromString('yesterday');
        $text = TodoText::fromString('Do something that will be forgotten');

        $events = [
            TodoWasPosted::byUser($userId, $text, $todoId, TodoStatus::OPEN()),
            DeadlineWasAddedToTodo::byUserToDate($todoId, $userId, $deadline),
        ];

        $todo = $this->reconstituteAggregateFromHistory(Todo::class, $events);

        $todo->markAsExpired();

        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(1, count($events));
        $this->assertInstanceOf(TodoWasMarkedAsExpired::class, $events[0]);

        $payload = $events[0]->payload();

        $this->assertArrayHasKey('old_status', $payload);
        $this->assertEquals('OPEN', $payload['old_status']);
        $this->assertArrayHasKey('new_status', $payload);
        $this->assertEquals('EXPIRED', $payload['new_status']);
        $this->assertArrayHasKey('assignee_id', $payload);
        $this->assertTrue(Uuid::isValid($payload['assignee_id']));

        return $todo;
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_marking_an_open_todo_before_the_deadline(): void
    {
        $todoId = TodoId::generate();
        $userId = UserId::generate();
        $deadline = TodoDeadline::fromString('2047-12-31 12:00:00');
        $text = TodoText::fromString('Do something before the deadline');
        $todo = Todo::post($text, $userId, $todoId);

        $todo->addDeadline($userId, $deadline);

        $this->expectException(TodoNotExpired::class);
        $this->expectExceptionMessage('Tried to mark a non-expired Todo as expired.  Todo will expire after '
            . 'the deadline 2047-12-31T12:00:00+00:00.');

        $todo->markAsExpired();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_marking_a_completed_todo_as_expired(): void
    {
        $todoId = TodoId::generate();
        $userId = UserId::generate();
        $deadline = TodoDeadline::fromString('2047-12-31 12:00:00');
        $text = TodoText::fromString('Do something fun');
        $todo = Todo::post($text, $userId, $todoId);

        $todo->addDeadline($userId, $deadline);
        $todo->markAsDone();

        $this->expectException(TodoNotOpen::class);
        $this->expectExceptionMessage('Tried to expire todo with status DONE.');

        $todo->markAsExpired();
    }

    /**
     * @test
     * @depends it_marks_an_open_todo_as_expired
     */
    public function it_throws_an_exception_when_marking_a_todo_as_expired_when_it_has_already_been_marked_as_expired(Todo $todo): void
    {
        $this->expectException(TodoNotOpen::class);
        $this->expectExceptionMessage('Tried to expire todo with status EXPIRED.');

        $todo->markAsExpired();
    }

    /**
     * @test
     * @depends it_marks_an_open_todo_as_expired
     */
    public function it_unmarks_an_expired_todo_when_deadline_is_added(Todo $todo): Todo
    {
        $userId = $todo->assigneeId();
        $deadline = TodoDeadline::fromString('1 day');

        $todo->addDeadline($userId, $deadline);

        $events = $this->popRecordedEvent($todo);

        $this->assertEquals(2, count($events));
        $this->assertInstanceOf(TodoWasUnmarkedAsExpired::class, $events[1]);

        return $todo;
    }
}

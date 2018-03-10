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

namespace Prooph\ProophessorDo\Model\Todo\Exception;

use Prooph\ProophessorDo\Model\Todo\Todo;
use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;

final class TodoNotOpen extends \RuntimeException
{
    public static function triedStatus(TodoStatus $status, Todo $todo): TodoNotOpen
    {
        return new self(sprintf(
            'Tried to change status of Todo %s to %s. But Todo is not marked as open!',
            $todo->todoId()->toString(),
            $status->toString()
        ));
    }

    public static function triedToAddDeadline(TodoDeadline $deadline, TodoStatus $status): TodoNotOpen
    {
        return new self(sprintf(
            'Tried to deadline %s to a todo with status %s.',
            $deadline->toString(),
            $status->toString()
        ));
    }

    public static function triedToAddReminder(TodoReminder $reminder, TodoStatus $status): TodoNotOpen
    {
        return new self(sprintf(
            'Tried to add reminder %s to a todo with status %s.',
            $reminder->toString(),
            $status->toString()
        ));
    }

    public static function triedToSendReminder(TodoReminder $reminder, TodoStatus $status): TodoNotOpen
    {
        return new self(sprintf(
            'Tried to send a reminder %s for a todo with status %s.',
            $reminder->toString(),
            $status->toString()
        ));
    }

    public static function triedToExpire(TodoStatus $status): TodoNotOpen
    {
        return new self(sprintf('Tried to expire todo with status %s.', $status->toString()));
    }
}

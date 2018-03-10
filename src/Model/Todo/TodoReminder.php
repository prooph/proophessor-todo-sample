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

use DateTimeImmutable;
use DateTimeZone;
use Prooph\ProophessorDo\Model\ValueObject;

final class TodoReminder implements ValueObject
{
    /**
     * @var \DateTimeImmutable
     */
    private $reminder;

    /**
     * @var TodoReminderStatus
     */
    private $status;

    public static function from(string $reminder, string $status): TodoReminder
    {
        return new self(
            new DateTimeImmutable($reminder, new DateTimeZone('UTC')),
            TodoReminderStatus::byName($status)
        );
    }

    private function __construct(DateTimeImmutable $reminder, TodoReminderStatus $status)
    {
        $this->reminder = $reminder;
        $this->status = $status;
    }

    public function isOpen(): bool
    {
        return $this->status->is(TodoReminderStatus::OPEN());
    }

    public function isInThePast(): bool
    {
        return $this->reminder < new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function isInTheFuture(): bool
    {
        return $this->reminder > new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function status(): TodoReminderStatus
    {
        return $this->status;
    }

    public function close(): TodoReminder
    {
        return new self($this->reminder, TodoReminderStatus::CLOSED());
    }

    public function toString(): string
    {
        return $this->reminder->format(\DateTime::ATOM);
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return get_class($this) === get_class($object)
            && $this->reminder->format('U.u') === $object->reminder->format('U.u')
            && $this->status->is($object->status);
    }
}

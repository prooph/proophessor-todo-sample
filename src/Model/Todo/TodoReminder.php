<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\ProophessorDo\Model\Todo;

use Prooph\ProophessorDo\Model\ValueObject;

/**
 * Class TodoReminder
 *
 * @package Prooph\ProophessorDo\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
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

    /**
     * @param string $reminder
     * @param string $status
     * @return TodoReminder
     */
    public static function from($reminder, $status)
    {
        return new self(
            new \DateTimeImmutable($reminder, new \DateTimeZone('UTC')),
            TodoReminderStatus::byValue($status)
        );
    }

    /**
     * @param string $reminder
     * @param TodoReminderStatus $status
     */
    private function __construct($reminder, TodoReminderStatus $status)
    {
        $this->reminder = $reminder;
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->status->is(TodoReminderStatus::OPEN());
    }

    /**
     * @return bool
     */
    public function isInThePast()
    {
        return $this->reminder < new \DateTimeImmutable("now", new \DateTimeZone('UTC'));
    }

    /**
     * @return bool
     */
    public function isInTheFuture()
    {
        return $this->reminder > new \DateTimeImmutable("now", new \DateTimeZone('UTC'));
    }

    /**
     * @return TodoReminderStatus
     */
    public function status()
    {
        return $this->status;
    }


    /**
     * @return TodoReminder
     */
    public function close()
    {
        return new self($this->reminder, TodoReminderStatus::CLOSED());
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->reminder->format(\DateTime::ATOM);
    }

    /**
     * @param ValueObject $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObject $object)
    {
        return get_class($this) === get_class($object)
            && $this->reminder->format('U.u') === $object->reminder->format('U.u')
            && $this->status->is($object->status);
    }
}

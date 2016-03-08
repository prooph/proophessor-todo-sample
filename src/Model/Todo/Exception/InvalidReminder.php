<?php

namespace Prooph\ProophessorDo\Model\Todo\Exception;

use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use Prooph\ProophessorDo\Model\User\UserId;

/**
 * Class InvalidReminder
 *
 * @package Prooph\ProophessorDo\Model\Todo\Exception
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
final class InvalidReminder extends \Exception
{
    /**
     * @param UserId $user
     * @param UserId $assigneeId
     * @return InvalidReminder
     */
    public static function userIsNotAssignee(UserId $user, UserId $assigneeId)
    {
        return new self(sprintf(
            'User %s tried to add a reminder to the todo owned by %s',
            $user->toString(),
            $assigneeId->toString()
        ));
    }


    /**
     * @param TodoReminder $reminder
     * @return InvalidReminder
     */
    public static function reminderInThePast(TodoReminder $reminder)
    {
        return new self(sprintf(
            'Provided reminder %s is in the past from %s',
            $reminder->toString(),
            $reminder->createdOn()
        ));
    }
    /**
     * @return InvalidReminder
     */
    public static function alreadyReminded()
    {
        return new self(sprintf(
            'The assignee was already reminded.'
        ));
    }
}

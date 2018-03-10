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

use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use Prooph\ProophessorDo\Model\User\UserId;

final class InvalidDeadline extends \Exception
{
    public static function userIsNotAssignee(UserId $user, UserId $assigneeId): InvalidDeadline
    {
        return new self(sprintf(
            'User %s tried to add a deadline to the todo owned by %s',
            $user->toString(),
            $assigneeId->toString()
        ));
    }

    public static function deadlineInThePast(TodoDeadline $deadline): InvalidDeadline
    {
        return new self(sprintf(
            'Provided deadline %s is in the past from %s',
            $deadline->toString(),
            $deadline->createdOn()
        ));
    }
}

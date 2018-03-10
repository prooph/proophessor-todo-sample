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

namespace Prooph\ProophessorDo\Model\Todo\Command;

use Prooph\Common\Messaging;
use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\User\UserId;

final class AddDeadlineToTodo extends Messaging\Command implements Messaging\PayloadConstructable
{
    use Messaging\PayloadTrait;

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function todoId(): TodoId
    {
        return TodoId::fromString($this->payload['todo_id']);
    }

    public function deadline(): TodoDeadline
    {
        return TodoDeadline::fromString($this->payload['deadline']);
    }
}

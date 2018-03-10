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

namespace Prooph\ProophessorDo\Model\User\Exception;

use Prooph\ProophessorDo\Model\User\UserId;

final class UserNotFound extends \InvalidArgumentException
{
    public static function withUserId(UserId $userId): UserNotFound
    {
        return new self(sprintf('User with id %s cannot be found.', $userId->toString()));
    }
}

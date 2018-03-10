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

namespace Prooph\ProophessorDo\Infrastructure\Service;

use Prooph\ProophessorDo\Model\User\EmailAddress;
use Prooph\ProophessorDo\Model\User\Service\ChecksUniqueUsersEmailAddress;
use Prooph\ProophessorDo\Model\User\UserId;
use Prooph\ProophessorDo\Projection\User\UserFinder;

final class ChecksUniqueUsersEmailAddressFromReadModel implements ChecksUniqueUsersEmailAddress
{
    /**
     * @var UserFinder
     */
    private $userFinder;

    public function __construct(UserFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function __invoke(EmailAddress $emailAddress): ?UserId
    {
        if ($user = $this->userFinder->findOneByEmailAddress($emailAddress->toString())) {
            return UserId::fromString($user->id);
        }

        return null;
    }
}

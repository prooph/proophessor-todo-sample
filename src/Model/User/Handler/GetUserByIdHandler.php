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

namespace Prooph\ProophessorDo\Model\User\Handler;

use Prooph\ProophessorDo\Model\User\Query\GetUserById;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use React\Promise\Deferred;

class GetUserByIdHandler
{
    private $userFinder;

    public function __construct(UserFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function __invoke(GetUserById $query, Deferred $deferred = null)
    {
        $user = $this->userFinder->findById($query->userId());
        if (null === $deferred) {
            return $user;
        }

        $deferred->resolve($user);
    }
}

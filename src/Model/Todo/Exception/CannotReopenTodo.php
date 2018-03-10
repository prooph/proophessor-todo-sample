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

final class CannotReopenTodo extends \RuntimeException
{
    public static function notMarkedDone(Todo $todo): CannotReopenTodo
    {
        return new self(sprintf(
            'Tried to reopen status of Todo %s. But Todo is not marked as done!',
            $todo->todoId()->toString()
        ));
    }
}

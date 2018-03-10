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

use Prooph\ProophessorDo\Model\Todo\TodoId;

final class TodoNotFound extends \InvalidArgumentException
{
    public static function withTodoId(TodoId $todoId): TodoNotFound
    {
        return new self(sprintf('Todo with id %s cannot be found.', $todoId->toString()));
    }
}

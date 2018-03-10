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

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Prooph\ProophessorDo\Model\Todo\TodoId;

final class MarkTodoAsDone extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function with(string $todoId): MarkTodoAsDone
    {
        return new self([
            'todo_id' => $todoId,
        ]);
    }

    public function todoId(): TodoId
    {
        return TodoId::fromString($this->payload['todo_id']);
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'todo_id');
        Assertion::uuid($payload['todo_id']);

        $this->payload = $payload;
    }
}

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

namespace ProophTest\ProophessorDo\Model\Todo\Command;

use Prooph\ProophessorDo\Model\Todo\Command\SendTodoReminderMail;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\User\UserId;
use ProophTest\ProophessorDo\TestCase;

final class SendTodoReminderMailTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_from_payload()
    {
        $userId = UserId::generate();
        $todoId = TodoId::generate();

        $command = new SendTodoReminderMail(
            [
                'user_id' => $userId->toString(),
                'todo_id' => $todoId->toString(),
            ]
        );

        $this->assertTrue($command->userId()->sameValueAs($userId));
        $this->assertTrue($command->todoId()->sameValueAs($todoId));
    }

    /**
     * @test
     */
    public function it_creates_from_factory_method()
    {
        $userId = UserId::generate();
        $todoId = TodoId::generate();

        $command = SendTodoReminderMail::with($userId, $todoId);

        $this->assertInstanceOf(SendTodoReminderMail::class, $command);
        $this->assertTrue($command->userId()->sameValueAs($userId));
        $this->assertTrue($command->todoId()->sameValueAs($todoId));
    }
}

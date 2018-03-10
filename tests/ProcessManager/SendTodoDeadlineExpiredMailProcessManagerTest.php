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

namespace ProophTest\ProophessorDo\ProcessManager;

use PHPUnit\Framework\TestCase;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;
use Prooph\ProophessorDo\Model\User\UserId;
use Prooph\ProophessorDo\ProcessManager\SendTodoDeadlineExpiredMailProcessManager;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;

class SendTodoDeadlineExpiredMailProcessManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_email_to_the_assignee_command(): void
    {
        $commandBus = $this->prophesize(CommandBus::class);
        $commandBus->dispatch(Argument::any())->shouldBeCalled();

        $processManager = new SendTodoDeadlineExpiredMailProcessManager($commandBus->reveal());

        $processManager(TodoWasMarkedAsExpired::fromStatus(
            TodoId::generate(),
            TodoStatus::OPEN(),
            TodoStatus::EXPIRED(),
            UserId::generate()
        ));
    }
}

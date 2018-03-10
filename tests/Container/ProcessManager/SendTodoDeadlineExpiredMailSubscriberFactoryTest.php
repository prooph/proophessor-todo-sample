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

namespace ProophTest\ProophessorDo\Container\ProcessManager;

use PHPUnit\Framework\TestCase;
use Prooph\ProophessorDo\Container\ProcessManager\SendTodoDeadlineExpiredMailSubscriberFactory;
use Prooph\ProophessorDo\ProcessManager\SendTodoDeadlineExpiredMailProcessManager;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

final class SendTodoDeadlineExpiredMailSubscriberFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_a_send_todo_deadline_expired_mail_process_manager(): void
    {
        /** @var CommandBus|ObjectProphecy $commandBus */
        $commandBus = $this->prophesize(CommandBus::class);
        /** @var ContainerInterface|ObjectProphecy $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(CommandBus::class)->willReturn($commandBus);

        $factory = new SendTodoDeadlineExpiredMailSubscriberFactory();

        $this->assertInstanceOf(SendTodoDeadlineExpiredMailProcessManager::class, $factory($container->reveal()));
    }
}

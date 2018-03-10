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

namespace ProophTest\ProophessorDo\Container\Model\Todo;

use PHPUnit\Framework\TestCase;
use Prooph\ProophessorDo\Container\Model\Todo\SendTodoReminderMailHandlerFactory;
use Prooph\ProophessorDo\Model\Todo\Handler\SendTodoReminderMailHandler;
use Prooph\ServiceBus\QueryBus;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Zend\Mail\Transport\TransportInterface;

final class SentTodoReminderMailHandlerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_a_the_handler(): void
    {
        /** @var QueryBus|ObjectProphecy $todoList */
        $queryBus = $this->prophesize(QueryBus::class);
        /** @var TransportInterface|ObjectProphecy $todoList */
        $transport = $this->prophesize(TransportInterface::class);
        /** @var ContainerInterface|ObjectProphecy $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(QueryBus::class)->willReturn($queryBus);
        $container->get(TransportInterface::class)->willReturn($transport);

        $factory = new SendTodoReminderMailHandlerFactory();

        $this->assertInstanceOf(SendTodoReminderMailHandler::class, $factory($container->reveal()));
    }
}

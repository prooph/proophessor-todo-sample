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
use Prooph\ProophessorDo\Container\Model\Todo\RemindTodoAssigneeHandlerFactory;
use Prooph\ProophessorDo\Model\Todo\Handler\RemindTodoAssigneeHandler;
use Prooph\ProophessorDo\Model\Todo\TodoList;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

final class RemindTodoAssigneeHandlerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_a_remind_todo_assignee_handler(): void
    {
        /** @var TodoList|ObjectProphecy $todoList */
        $todoList = $this->prophesize(TodoList::class);
        /** @var ContainerInterface|ObjectProphecy $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(TodoList::class)->willReturn($todoList);

        $factory = new RemindTodoAssigneeHandlerFactory();

        $this->assertInstanceOf(RemindTodoAssigneeHandler::class, $factory($container->reveal()));
    }
}

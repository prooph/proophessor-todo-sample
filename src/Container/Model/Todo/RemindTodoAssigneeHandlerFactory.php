<?php

namespace Prooph\ProophessorDo\Container\Model\Todo;

use Interop\Container\ContainerInterface;
use Prooph\ProophessorDo\Model\Todo\Handler\RemindTodoAssigneeHandler;
use Prooph\ProophessorDo\Model\Todo\TodoList;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;

/**
 * Class RemindTodoAssigneeHandlerFactory
 *
 * @package Prooph\ProophessorDo\Container\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
class RemindTodoAssigneeHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return RemindTodoAssigneeHandlerFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RemindTodoAssigneeHandler($container->get(TodoList::class), $container->get(TodoFinder::class));
    }
}

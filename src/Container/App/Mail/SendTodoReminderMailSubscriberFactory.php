<?php

namespace Prooph\ProophessorDo\Container\App\Mail;

use Interop\Container\ContainerInterface;
use Prooph\ProophessorDo\App\Mail\SendTodoReminderMailSubscriber;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;

/**
 * Class RemindTodoAssigneeHandlerFactory
 *
 * @package Prooph\ProophessorDo\Container\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
class SendTodoReminderMailSubscriberFactory
{
    /**
     * @param ContainerInterface $container
     * @return SendTodoReminderMailSubscriber
     */
    public function __invoke(ContainerInterface $container)
    {
        return new SendTodoReminderMailSubscriber($container->get(UserFinder::class), $container->get(TodoFinder::class));
    }
}

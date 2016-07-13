<?php
namespace Prooph\ProophessorDo\Container\ProcessManager;

use Interop\Container\ContainerInterface;
use Prooph\ProophessorDo\ProcessManager\SendTodoDeadlineExpiredMailSubscriber;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Zend\Mail\Transport\TransportInterface;


/**
 * Class SendTodoDeadlineExpiredMailSubscriberFactory
 *
 * @package Prooph\ProophessorDo\Container\Model\Todo
 * @author Michał Żukowski <michal@durooil.com>
 */
final class SendTodoDeadlineExpiredMailSubscriberFactory
{
    /**
     * @param ContainerInterface $container
     * @return SendTodoDeadlineExpiredMailSubscriber
     */
    public function __invoke(ContainerInterface $container)
    {
        return new SendTodoDeadlineExpiredMailSubscriber(
            $container->get(UserFinder::class),
            $container->get(TodoFinder::class),
            $container->get(TransportInterface::class)
        );
    }
}

<?php
namespace Prooph\ProophessorDo\App\Mail;

use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;

/**
 * Class SendTodoReminderMailSubscriber
 *
 * @package Prooph\ProophessorDo\App\Mail
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
class SendTodoReminderMailSubscriber
{
    /**
     * @var UserFinder
     */
    private $userFinder;
    /**
     * @var TodoFinder
     */
    private $todoFinder;

    /**
     * @param UserFinder $userFinder
     * @param TodoFinder $todoFinder
     */
    public function __construct(UserFinder $userFinder, TodoFinder $todoFinder)
    {
        $this->userFinder = $userFinder;
        $this->todoFinder = $todoFinder;
    }

    public function __invoke(TodoAssigneeWasReminded $event)
    {
        $user = $this->userFinder->findById($event->userId()->toString());
        $todo = $this->todoFinder->findById($event->todoId()->toString());

        $mail = "Hello {$user->name}. This a reminder for {$todo->text}";
        echo "Sending mail to {$user->email}\n";
        echo " {$mail}\n\n";
    }
}

<?php
namespace Prooph\ProophessorDo\ProcessManager;

use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail;

/**
 * Class SendTodoReminderMailSubscriber
 *
 * @package Prooph\ProophessorDo\App\Mail
 * @author Roman Sachse <r.sachse@ipark-media.de>
 */
final class SendTodoReminderMailSubscriber
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
     * @var TransportInterface
     */
    private $mailer;

    /**
     * @param UserFinder $userFinder
     * @param TodoFinder $todoFinder
     * @param TransportInterface $mailer
     */
    public function __construct(UserFinder $userFinder, TodoFinder $todoFinder, TransportInterface $mailer)
    {
        $this->userFinder = $userFinder;
        $this->todoFinder = $todoFinder;
        $this->mailer = $mailer;
    }

    /**
     * @param TodoAssigneeWasReminded $event
     */
    public function __invoke(TodoAssigneeWasReminded $event)
    {
        $user = $this->userFinder->findById($event->userId()->toString());
        $todo = $this->todoFinder->findById($event->todoId()->toString());

        $mail = new Mail\Message();
        $mail->setBody("Hello {$user->name}. This a reminder for '{$todo->text}'. Don't be lazy!");
        $mail->setFrom('reminder@getprooph.org', 'Proophessor-do');
        $mail->addTo($user->email, $user->name);
        $mail->setSubject('Proophessor-do Todo Reminder');

        $this->mailer->send($mail);
    }
}

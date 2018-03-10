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

namespace Prooph\ProophessorDo\Model\Todo\Handler;

use Prooph\ProophessorDo\Model\Todo\Command\SendTodoReminderMail;
use Prooph\ProophessorDo\Model\Todo\Query\GetTodoById;
use Prooph\ProophessorDo\Model\User\Query\GetUserById;
use Prooph\ServiceBus\QueryBus;
use Zend\Mail;
use Zend\Mail\Transport\TransportInterface;

class SendTodoReminderMailHandler
{
    /**
     * @var QueryBus
     */
    private $queryBus;
    /**
     * @var TransportInterface
     */
    private $mailer;

    public function __construct(QueryBus $queryBus, TransportInterface $mailer)
    {
        $this->queryBus = $queryBus;
        $this->mailer = $mailer;
    }

    public function __invoke(SendTodoReminderMail $command): void
    {
        $user = $this->getUser($command->userId()->toString());
        if (! $user) {
            return;
        }

        $todo = $this->getTodo($command->todoId()->toString());
        if (! $todo) {
            return;
        }

        $mail = new Mail\Message();
        $mail->setBody("Hello {$user->name}. This a reminder for '{$todo->text}'. Don't be lazy!");
        $mail->setFrom('reminder@localhost', 'Proophessor-do');
        $mail->addTo($user->email, $user->name);
        $mail->setSubject('Proophessor-do Todo Reminder');

        $this->mailer->send($mail);
    }

    private function getUser(string $userId): ?\stdClass
    {
        $user = null;
        $this->queryBus
            ->dispatch(new GetUserById($userId))
            ->then(
                function (\stdClass $result = null) use (&$user) {
                    $user = $result;
                }
            );

        return $user;
    }

    private function getTodo(string $todoId): ?\stdClass
    {
        $todo = null;
        $this->queryBus
            ->dispatch(new GetTodoById($todoId))
            ->then(
                function (\stdClass $result = null) use (&$todo) {
                    $todo = $result;
                }
            );

        return $todo;
    }
}

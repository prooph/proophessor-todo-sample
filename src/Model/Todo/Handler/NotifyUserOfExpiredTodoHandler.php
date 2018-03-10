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

use Prooph\ProophessorDo\Model\Todo\Command\NotifyUserOfExpiredTodo;
use Prooph\ProophessorDo\Model\Todo\Query\GetTodoById;
use Prooph\ProophessorDo\Model\User\Query\GetUserById;
use Prooph\ServiceBus\QueryBus;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class NotifyUserOfExpiredTodoHandler
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var TransportInterface
     */
    private $mailer;

    public function __construct(
        QueryBus $queryBus,
        TransportInterface $mailer
    ) {
        $this->queryBus = $queryBus;
        $this->mailer = $mailer;
    }

    public function __invoke(NotifyUserOfExpiredTodo $command): void
    {
        $todo = $this->getTodo($command->todoId()->toString());
        if (! $todo) {
            return;
        }

        $user = $this->getUser($todo->assignee_id);
        if (! $user) {
            return;
        }

        $message = sprintf(
            'Hi %s! Just a heads up: your todo `%s` has expired on %s.',
            $user->name,
            $todo->text,
            $todo->deadline
        );

        $mail = new Message();
        $mail->setBody($message);
        $mail->setEncoding('utf-8');
        $mail->setFrom('reminder@localhost', 'Proophessor-do');
        $mail->addTo($user->email, $user->name);
        $mail->setSubject('Proophessor-do Todo expired');

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
        $user = null;
        $this->queryBus
            ->dispatch(new GetTodoById($todoId))
            ->then(
                function (\stdClass $result = null) use (&$todo) {
                    $todo = $result;
                }
            );

        return $user;
    }
}

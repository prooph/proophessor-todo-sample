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

namespace Prooph\ProophessorDo\ProcessManager;

use Prooph\ProophessorDo\Model\Todo\Command\NotifyUserOfExpiredTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ServiceBus\CommandBus;

class SendTodoDeadlineExpiredMailProcessManager
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(TodoWasMarkedAsExpired $event): void
    {
        $this->commandBus->dispatch(NotifyUserOfExpiredTodo::with($event->todoId()));
    }
}

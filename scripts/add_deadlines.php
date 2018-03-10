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

/**
 * Pick random todo from database and add 500 deadline events
 */
namespace {
    use Prooph\ProophessorDo\Model\Todo\Command\AddDeadlineToTodo;
    use Prooph\ProophessorDo\Projection\Todo\TodoFinder;

    define('NUMBER_OF_DEADLINES', 100);

    chdir(dirname(__DIR__));

    // Setup autoloading
    require 'vendor/autoload.php';

    $container = require 'config/container.php';

    /** @var $todoFinder TodoFinder */
    $todoFinder = $container->get(TodoFinder::class);

    $allTodos = $todoFinder->findAllOpen();

    $numOfTodos = count($allTodos);

    if ($numOfTodos === 0) {
        echo 'No open todos available in the database. Please add at least on open todo before running the script.';
        exit(1);
    }

    $randomIndex = random_int(0, --$numOfTodos);

    $todo = $allTodos[$randomIndex];

    echo 'Randomly selected todo: ' . $todo->id . "\n";
    echo 'Going to add '.NUMBER_OF_DEADLINES." deadline events now\n";
    $commandBus = $container->get(\Prooph\ServiceBus\CommandBus::class);

    $nextDay = new \DateTimeImmutable();
    $oneDate = new \DateInterval('P1D');

    for ($i = 0;$i < NUMBER_OF_DEADLINES;$i++) {
        $nextDay = $nextDay->add($oneDate);

        $addDeadline = new AddDeadlineToTodo([
            'todo_id' => $todo->id,
            'user_id' => $todo->assignee_id,
            'deadline' => $nextDay->format(\DateTime::ATOM),
        ]);

        $commandBus->dispatch($addDeadline);
    }

    echo 'All deadlines successfully added.';
}

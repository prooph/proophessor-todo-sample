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
 * This script creates NUMBER_OF_USERS and then it performs NUMBER_OF_TODO_PER_USER
 */
namespace {

    use Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsDone;
    use Prooph\ProophessorDo\Model\Todo\Command\PostTodo;
    use Prooph\ProophessorDo\Model\Todo\TodoId;
    use Prooph\ProophessorDo\Model\User\Command\RegisterUser;
    use Prooph\ProophessorDo\Model\User\UserId;
    use Symfony\Component\Stopwatch\Stopwatch;

    define('NUMBER_OF_USERS', 10);
    define('NUMBER_OF_TODO_PER_USER', 5);

    chdir(dirname(__DIR__));

    // Setup autoloading
    require 'vendor/autoload.php';

    function getUserName($pos)
    {
        //top 10 taken from: http://www.beliebte-vornamen.de/jahrgang/j2013/top500-2013
        static $names = [
            'Mia', 'Ben', 'Emma', 'Luca', 'Hannah', 'Paul', 'Sofia', 'Jonas', 'Anna', 'Finn', 'Lea', 'Leon', 'Emilia',
            'Luis', 'Marie', 'Lukas', 'Lena', 'Maximilian', 'Leonie', 'Felix',
        ];

        $factor = 0;
        if ($pos > 20) {
            $factor = floor($pos / 20);
            $pos = $pos - (20 * $factor);
            if (! $pos) {
                $pos = 20;
                $factor--;
            }
        }

        $index = $pos - 1;

        return $factor > 0 ? $names[$index].($factor + 1) : $names[$index];
    }

    function randTodoText()
    {
        static $texts = [
            'Start reading a new book', 'Contribute to prooph', 'Shutdown PC and go out', 'Try prooph/event-store',
            'Learn more about DDD', 'Work on a proophessor-do exercise', 'Say hello in prooph chat',
            'Integrate prooph components in project', 'Try async messaging', 'Prepare slides for a talk',
            'Submit idea for a new component to prooph wish list', 'Try prooph/service-bus', 'Buy some milk',
            'Analyse infrastructure to identify bottlenecks', 'Thank people at prooph for the great work',
            'Read something about CQRS', 'Drink a beer with a friend', 'Help people who need support',
            'Tackle complex problems with DDD and event sourcing', 'Visit family', 'Tweet about playing with prooph components',
        ];

        $textCount = count($texts);

        $randIndex = random_int(0, --$textCount);

        return $texts[$randIndex];
    }

    function calcCommandAverage($duration)
    {
        $commandCount = NUMBER_OF_USERS * NUMBER_OF_TODO_PER_USER /*Open todo*/ + NUMBER_OF_USERS /*Create User*/;

        return $duration / $commandCount;
    }

    $container = require 'config/container.php';

    $commandBus = $container->get(\Prooph\ServiceBus\CommandBus::class);

    $openTodos = [];

    $stopWatch = new Stopwatch();

    $stopWatch->start('generate_model');

    for ($i = 1;$i <= NUMBER_OF_USERS;$i++) {
        $userId = UserId::generate();
        $username = getUserName($i);
        $email = $username . '@acme.com';

        $commandBus->dispatch(RegisterUser::withData($userId->toString(), $username, $email));

        for ($j = 0;$j < NUMBER_OF_TODO_PER_USER;$j++) {
            $todoId = TodoId::generate();

            $commandBus->dispatch(PostTodo::forUser($userId->toString(), randTodoText(), $todoId->toString()));

            $openTodos[] = $todoId->toString();
        }

        echo 'User: ' . $username . '('.$userId->toString().") was registered\n";
    }

    $generatedEvent = $stopWatch->stop('generate_model');

    echo "\nModel generated in: " . $generatedEvent->getDuration() . " ms\n";
    echo 'Command execution average: ' . calcCommandAverage($generatedEvent->getDuration()) . " ms\n";
    echo "\nGoing to close todo randomly now\n";

    foreach ($openTodos as $openTodoId) {
        $close = random_int(0, 1);

        if ($close) {
            $commandBus->dispatch(MarkTodoAsDone::with($openTodoId));
        }
    }

    echo 'Done! Check out proophessor-do';
}

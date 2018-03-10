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
 * Pass a todo id to the script to take a snapshot of the todo.
 * Note: A snapshot adapter needs to be configured!
 */
namespace {
    use Prooph\EventSourcing\Aggregate\AggregateType;
    use Prooph\ProophessorDo\Model\Todo\Todo;
    use Prooph\ProophessorDo\Model\Todo\TodoId;
    use Prooph\ProophessorDo\Model\Todo\TodoList;
    use Prooph\SnapshotStore\Snapshot;
    use Prooph\SnapshotStore\SnapshotStore;

    chdir(dirname(__DIR__));

    // Setup autoloading
    require 'vendor/autoload.php';

    /**
     * @param Todo $todo
     * @return int
     */
    function get_todo_version(Todo $todo)
    {
        $todoReflected = new \ReflectionClass($todo);
        $versionProp = $todoReflected->getProperty('version');
        $versionProp->setAccessible(true);

        return $versionProp->getValue($todo);
    }

    $container = require 'config/container.php';

    array_shift($argv);

    if (empty($argv)) {
        echo "\033[1;31mMissing todo id parameter!\033[0m\n";
        exit(1);
    }

    $todoId = $argv[0];

    try {
        $todoId = TodoId::fromString($todoId);
    } catch (\Exception $ex) {
        echo "\033[1;31mInvalid todo id given!\033[0m\n";
        exit(1);
    }

    /** @var $todoList TodoList */
    $todoList = $container->get(TodoList::class);

    $todo = $todoList->get($todoId);

    if (null === $todo) {
        echo "\033[1;31mTodo could not be found!\033[0m\n";
        exit(1);
    }

    /** @var $snapshotStore SnapshotStore */
    $snapshotStore = $container->get(SnapshotStore::class);

    $snapshot = new Snapshot(
        (string) AggregateType::fromAggregateRoot($todo),
        $todoId->toString(),
        $todo,
        get_todo_version($todo),
        new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
    );

    $snapshotStore->save($snapshot);

    echo "Snapshot was taken!\n";
}

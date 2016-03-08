<?php
/**
 * This script looks for todos with open reminders and reminds the assignees
 */
namespace {

    use Prooph\ProophessorDo\Model\Todo\Command\RemindTodoAssignee;
    use Prooph\ProophessorDo\Model\Todo\TodoId;
    use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
    use Prooph\ServiceBus\CommandBus;

    chdir(dirname(__DIR__));

    // Setup autoloading
    require 'vendor/autoload.php';

    $container = require 'config/container.php';

    $commandBus = $container->get(CommandBus::class);
    $todoFinder = $container->get(TodoFinder::class);

    $todos = $todoFinder->findByOpenReminders();

    if (!$todos) {
        echo "Nothing todo. Exiting.\n";
        exit;
    }

    foreach ($todos as $todo) {
        echo "Send reminder for Todo with id {$todo->id}.\n";
        $commandBus->dispatch(RemindTodoAssignee::forTodo(TodoId::fromString($todo->id)));
    }

    echo "Done!\n";
}

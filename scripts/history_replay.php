<?php

/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 10/22/15 - 10:39 PM
 */

/**
 * Replay all events to regenerate the read model
 *
 */

namespace {

    use Prooph\EventStore\EventStore;
    use Prooph\EventStore\Stream\StreamName;
    use Prooph\ProophessorDo\Projection\Table;
    use Prooph\ProophessorDo\Container\App\EventBus\ReplayBus;
    use Prooph\ServiceBus\Exception\MessageDispatchException;
    use Prooph\ServiceBus\MessageBus;
    use Symfony\Component\Stopwatch\Stopwatch;

chdir(dirname(__DIR__));

    // Setup autoloading
    require 'vendor/autoload.php';

    $container = require 'config/container.php';

    /** @var $dbalConnection Connection */
    $dbalConnection = $container->get('doctrine.connection.default');

    //Make sure that the read model tables are empty
    $dbalConnection->exec('TRUNCATE TABLE ' . Table::USER);
    $dbalConnection->exec('TRUNCATE TABLE ' . Table::TODO);
    $dbalConnection->exec('TRUNCATE TABLE ' . Table::TODO_REMINDER);

    /** @var $eventStore EventStore */
    $eventStore = $container->get(EventStore::class);

    /** @var $replayBus ReplayBus */
    $replayBus = $container->build(ReplayBus::class);


    $retry = function (array $failedEvents, $retryCount, \Exception $lastException, callable $retryCb) use ($replayBus) {
        if ($retryCount > 3) {
            echo "\033[1;31mAborting retry... Something unexpected happen.\033[0m\n\n";
            if ($lastException instanceof MessageDispatchException) {
                throw $lastException->getFailedDispatchEvent()->getParam(MessageBus::EVENT_PARAM_EXCEPTION);
            }
        }

        $newFailedEvents = [];
        foreach ($failedEvents as $failedEvent) {
            try {
                $replayBus->dispatch($failedEvent);
            } catch (\Exception $ex) {
                $newFailedEvents[] = $failedEvent;
            }
        }

        if (count($newFailedEvents)) {
            $retryCb($newFailedEvents, ++$retryCount, $ex, $retryCb);
        }
    };

    $stopWatch = new Stopwatch();

    $stopWatch->start('replay');

    //Let's replay
    $replayStream = $eventStore->replay([new StreamName('event_stream')]);

    $failedEvents = [];
    foreach ($replayStream as $recordedEvent) {
        try {
            $replayBus->dispatch($recordedEvent);
        } catch (\Exception $ex) {
            $failedEvents[] = $recordedEvent;
        }
    }

    if (count($failedEvents)) {
        echo "\033[0;31mSome events didn't make it. Going to retry " . count($failedEvents) . " events\033[0m\n";
        $retry($failedEvents, 1, $ex, $retry);
    }

    $replayWatchEvent = $stopWatch->stop('replay');

    echo "Replay done in " . $replayWatchEvent->getDuration() . " ms";
}

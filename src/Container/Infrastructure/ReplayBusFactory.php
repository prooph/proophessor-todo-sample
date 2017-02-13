<?php

/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\ProophessorDo\Container\Infrastructure;

use Prooph\ServiceBus\Container\EventBusFactory;

/**
 * Class ReplayBusFactory
 *
 * @package src\Infrastructure\Container
 * @author Djaimy Hoogerwerf <djaimy@codebridge.nl>
 */
class ReplayBusFactory extends EventBusFactory {

    /**
     * @inheritdoc
     */
    public function __construct($configId = 'replay_bus') {
        parent::__construct($configId);
    }

    /**
     * @inheritdoc
     */
    protected function getBusClass() {
        return ReplayBus::class;
    }

}

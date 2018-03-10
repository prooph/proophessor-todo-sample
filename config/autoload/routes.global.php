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

namespace Prooph\ProophessorDo;

use Zend\Expressive;

return [
    'dependencies' => [
        'invokables' => [
            Expressive\Router\RouterInterface::class => Expressive\Router\AuraRouter::class,
        ],
        'factories' => [
            \Prooph\ProophessorDo\Middleware\JsonPayload::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
];

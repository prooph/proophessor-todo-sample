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

namespace Prooph\ProophessorDo\Container;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

class MongoClientFactory implements RequiresConfig, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    public function __invoke(ContainerInterface $container): Client
    {
        $config = $this->options($container->get('config'));

        return new Client($config['uri'], $config['uri_options'], $config['driver_options']);
    }

    public function dimensions(): iterable
    {
        return ['proophessor-do', 'mongo_client'];
    }

    public function defaultOptions(): iterable
    {
        return [
            'uri' => 'mongodb://127.0.0.1/',
            'uri_options' => [],
            'driver_options' => [],
        ];
    }
}

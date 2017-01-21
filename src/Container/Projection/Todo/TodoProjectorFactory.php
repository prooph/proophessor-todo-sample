<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prooph\ProophessorDo\Container\Projection\Todo;

use Interop\Container\ContainerInterface;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\Todo\TodoProjector;

/**
 * Class TodoProjectorFactory
 *
 * @package Prooph\ProophessorDo\Projection\Todo
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
final class TodoProjectorFactory
{
    /**
     * @param ContainerInterface $container
     * @return TodoProjector
     */
    public function __invoke(ContainerInterface $container)
    {
        return new TodoProjector(
            $container->get('doctrine.connection.default'),
            $container->get(TodoFinder::class)
        );
    }
}

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

namespace Prooph\ProophessorDo\Container\Console;

use Prooph\Cli\Console\Helper\Psr4Info;
use Psr\Container\ContainerInterface;

class Psr4ClassInfoFactory
{
    public function __invoke(ContainerInterface $container): Psr4Info
    {
        $docblock = <<<'PROOPH'
This file is part of prooph/proophessor.
(c) 2014-2017 prooph software GmbH <contact@prooph.de>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
PROOPH;

        return new Psr4Info(
            'src',
            'Prooph\\ProophessorDo\\',
            $docblock
        );
    }
}

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

namespace Prooph\ProophessorDo\Container\App\Action;

use Prooph\ProophessorDo\App\Action\UserTodoList;
use Prooph\ServiceBus\QueryBus;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class UserTodoListFactory
{
    public function __invoke(ContainerInterface $container): UserTodoList
    {
        return new UserTodoList(
            $container->get(TemplateRendererInterface::class),
            $container->get(QueryBus::class)
        );
    }
}

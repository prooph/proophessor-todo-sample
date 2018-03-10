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

namespace Prooph\ProophessorDo\Container\App\View;

use Psr\Container\ContainerInterface;
use Zend\View\HelperPluginManager;

class ViewHelperPluginManagerFactory
{
    /**
     * @throws \RuntimeException
     */
    public function __invoke(ContainerInterface $container): HelperPluginManager
    {
        $helperPluginManager = new HelperPluginManager();

        $config = $container->has('config') ? $container->get('config') : [];

        if (isset($config['templates']['plugins'])) {
            foreach ($config['templates']['plugins'] as $pluginName => $serviceName) {
                $helperPluginManager->setFactory($pluginName, function () use ($container, $serviceName) {
                    return $container->get($serviceName);
                });
            }
        }

        return $helperPluginManager;
    }
}

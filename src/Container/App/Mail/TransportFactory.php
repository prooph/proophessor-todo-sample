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

namespace Prooph\ProophessorDo\Container\App\Mail;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;
use Psr\Container\ContainerInterface;
use Zend\Mail\Transport\InMemory as InMemoryTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\TransportInterface;

class TransportFactory implements RequiresConfig, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    public function dimensions(): iterable
    {
        return ['proophessor-do', 'mail'];
    }

    public function defaultOptions(): iterable
    {
        return ['transport' => 'in_memory'];
    }

    public function __invoke(ContainerInterface $container): TransportInterface
    {
        return $this->getTransport($container->get('config'));
    }

    private function getTransport($config): TransportInterface
    {
        $config = $this->optionsWithFallback($config);

        switch ($config['transport']) {
            case 'in_memory':
                return new InMemoryTransport();

            case 'smtp':
                $transport = new SmtpTransport();
                $transport->setOptions(new SmtpOptions($config['smtp']));

                return $transport;
        }
        throw new \InvalidArgumentException(
            "Transport of type '{$config['transport']}' not known (use in_memory or smtp)"
        );
    }
}

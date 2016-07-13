<?php

namespace Prooph\ProophessorDo\Container\App\Mail;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\InMemory as InMemoryTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\TransportInterface;


/**
 * Class TransportFactory
 *
 * @package Prooph\ProophessorDo\Container\Model\Todo
 * @author Michał Żukowski <michal@durooil.com>
 */
final class TransportFactory implements RequiresConfig, RequiresMandatoryOptions, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    /**
     * @inheritdoc
     */
    public function dimensions()
    {
        return ['proophessor-do', 'mail'];
    }

    /**
     * @inheritdoc
     */
    public function mandatoryOptions()
    {
        return ['transport'];
    }

    /**
     * @inheritdoc
     */
    public function defaultOptions()
    {
        return ['transport' => 'in_memory'];
    }

    /**
     * @param ContainerInterface $container
     * @return TransportInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        return $this->getTransport($container->get('config'));
    }

    private function getTransport($config)
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

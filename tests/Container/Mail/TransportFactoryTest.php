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

namespace ProophTest\ProophessorDo\Container\Mail;

use PHPUnit\Framework\TestCase;
use Prooph\ProophessorDo\Container\App\Mail\TransportFactory;
use Psr\Container\ContainerInterface;
use Zend\Mail\Transport\InMemory;
use Zend\Mail\Transport\Smtp;

class TransportFactoryTest extends TestCase
{
    /** @var TransportFactory */
    private $factory;

    /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $containerMock;

    protected function setUp(): void
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->factory = new TransportFactory();
    }

    /**
     * @test
     */
    public function test_it_returns_in_memory_transport_by_default(): void
    {
        $factory = $this->factory;

        $this->assertInstanceOf(InMemory::class, $factory($this->containerMock));
    }

    /**
     * @test
     */
    public function it_returns_in_memory_transport(): void
    {
        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'in_memory',
                    ],
                ],
            ]));

        $factory = $this->factory;

        $this->assertInstanceOf(InMemory::class, $factory($this->containerMock));
    }

    /**
     * @test
     */
    public function it_returns_smtp_transport(): void
    {
        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'smtp',
                        'smtp' => [],
                    ],
                ],
            ]));

        $factory = $this->factory;

        $this->assertInstanceOf(Smtp::class, $factory($this->containerMock));
    }

    /**
     * @test
     */
    public function test_it_throws_exception_on_not_implemented_transport(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'missing',
                    ],
                ],
            ]));

        $factory = $this->factory;
        $factory($this->containerMock);
    }
}

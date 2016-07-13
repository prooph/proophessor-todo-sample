<?php
namespace ProophTest\ProophessorDo\Container\Mail;

use Interop\Container\ContainerInterface;
use Prooph\ProophessorDo\Container\App\Mail\TransportFactory;
use Zend\Mail\Transport\InMemory;
use Zend\Mail\Transport\Smtp;

class TransportFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var TransportFactory */
    private $factory;

    /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $containerMock;

    public function setUp()
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->factory = new TransportFactory();
    }

    /**
     * @test
     */
    public function test_it_returns_in_memory_transport_by_default()
    {
        $factory = $this->factory;

        $this->assertInstanceOf(InMemory::class, $factory($this->containerMock));
    }

    /**
     * @test
     */
    public function it_returns_in_memory_transport()
    {
        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'in_memory',
                    ]
                ]
            ]));

        $factory = $this->factory;

        $this->assertInstanceOf(InMemory::class, $factory($this->containerMock));
    }

    /**
     * @test
     */
    public function it_returns_smtp_transport()
    {
        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'smtp',
                        'smtp' => [],
                    ]
                ]
            ]));

        $factory = $this->factory;

        $this->assertInstanceOf(Smtp::class, $factory($this->containerMock));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_exception_on_not_implemented_transport()
    {
        $this->containerMock
            ->method('get')
            ->will($this->returnValue([
                'proophessor-do' => [
                    'mail' => [
                        'transport' => 'missing',
                    ]
                ]
            ]));

        $factory = $this->factory;
        $factory($this->containerMock);
    }
}

<?php
namespace ProophTest\ProophessorDo\ProcessManager;

use Prooph\ProophessorDo\ProcessManager\SendTodoDeadlineExpiredMailSubscriber;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Rhumsaa\Uuid\Uuid;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class SendTodoDeadlineExpiredMailSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var TodoFinder|\PHPUnit_Framework_MockObject_MockObject */
    private $todoFinder;

    /** @var UserFinder|\PHPUnit_Framework_MockObject_MockObject */
    private $userFinder;

    /** @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mailer;

    /** @var SendTodoDeadlineExpiredMailSubscriber */
    private $action;

    public function setUp()
    {
        $this->userFinder = $this->getMockBuilder(UserFinder::class)
            ->disableOriginalConstructor()
            ->setMethods(['findById'])
            ->getMock();

        $this->todoFinder = $this->getMockBuilder(TodoFinder::class)
            ->disableOriginalConstructor()
            ->setMethods(['findById'])
            ->getMock();

        $this->mailer = $this->getMockBuilder(TransportInterface::class)
            ->setMethods(['send'])
            ->getMockForAbstractClass();

        $this->action = new SendTodoDeadlineExpiredMailSubscriber(
            $this->userFinder,
            $this->todoFinder,
            $this->mailer
        );
    }

    /**
     * @test
     * @dataProvider objectsData
     * @param \stdClass $todo
     * @param \stdClass $user
     */
    public function it_sends_email_to_the_assignee(\stdClass $todo, \stdClass $user)
    {
        $this->todoFinder->expects($this->once())
            ->method('findById')
            ->with($todo->id)
            ->will($this->returnValue($todo));

        $this->userFinder->expects($this->once())
            ->method('findById')
            ->with($user->id)
            ->will($this->returnValue($user));

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Message::class));

        $action = $this->action;
        $action($this->getEvent($todo->id));
    }

    public function objectsData()
    {
        $todo = [
            'id'          => Uuid::uuid4()->toString(),
            'assignee_id' => Uuid::uuid4()->toString(),
            'text'        => 'text',
            'deadline'    => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        $user = [
            'id'    => $todo['assignee_id'],
            'name'  => 'Mike',
            'email' => 'mike@fakedomain.com'
        ];

        return [
            [(object)$todo, (object)$user],
        ];
    }

    public function getEvent($todoId)
    {
        return TodoWasMarkedAsExpired::fromStatus(
            TodoId::fromString($todoId),
            TodoStatus::fromString(TodoStatus::OPEN),
            TodoStatus::fromString(TodoStatus::EXPIRED)
        );
    }
}

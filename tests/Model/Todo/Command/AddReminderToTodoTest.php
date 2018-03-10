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

namespace ProophTest\ProophessorDo\Model\Todo\Command;

use Prooph\ProophessorDo\Model\Todo\Command\AddReminderToTodo;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use ProophTest\ProophessorDo\TestCase;

/**
 * Class AddReminderToTodoTest
 *
 * @package ProophTest\ProophessorDo\Model\Todo\Command
 * @author Aleksei Akireikin <opexus@gmail.com>
 */
class AddReminderToTodoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_reminder_from_payload()
    {
        $command = new AddReminderToTodo(['reminder' => '2017-02-14T05:00:00.000Z']);
        $reminder = $command->reminder();
        $this->assertInstanceOf(TodoReminder::class, $reminder);
        $this->assertTrue($reminder->isOpen());
        $this->assertSame('2017-02-14T05:00:00+00:00', $reminder->toString());
    }
}

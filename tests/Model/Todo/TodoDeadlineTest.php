<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\ProophessorDo\Model\Todo;

use Prooph\ProophessorDo\Model\Todo\TodoDeadline;
use ProophTest\ProophessorDo\TestCase;

class TodoDeadlineTest extends TestCase
{
    /**
     * @test
     * @dataProvider getDeadlines
     */
    public function it_correctly_validates_the_deadline($deadline, $inThePast): void
    {
        $deadline = TodoDeadline::fromString($deadline);
        $deadlineInThePast = $deadline->isInThePast();

        if ($inThePast) {
            $this->assertTrue($deadlineInThePast);
        } else {
            $this->assertFalse($deadlineInThePast);
        }
    }

    /**
     * @test
     * @dataProvider getDeadlines
    */
    public function it_correctly_validates_the_deadline_is_met($deadline, $inThePast): void
    {
        $deadline = TodoDeadline::fromString($deadline);
        $isMet = $deadline->isMet();

        if ($inThePast) {
            $this->assertFalse($isMet, 'Deadline is not met');
        } else {
            $this->assertTrue($isMet, 'Deadline is met');
        }
    }


    public function getDeadlines(): array
    {
        return [
            [
                '2049-12-15T17:19:27+01:00',
                false,
            ],
            [
                '2037-01-01 10:00:00',
                false,
            ],
            [
                '2017-12-15T17:19:27+01:00',
                true,
            ],
            [
                '1947-01-01 10:00:00',
                true,
            ],
        ];
    }
}

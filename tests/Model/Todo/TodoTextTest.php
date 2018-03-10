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

namespace ProophTest\ProophessorDo\Model\Todo;

use Prooph\ProophessorDo\Model\Todo\Exception\InvalidText;
use Prooph\ProophessorDo\Model\Todo\TodoText;
use Prooph\ProophessorDo\Model\ValueObject;
use ProophTest\ProophessorDo\TestCase;

class TodoTextTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_text_from_string()
    {
        $text = TodoText::fromString('foo');
        $this->assertSame('foo', $text->toString());
    }

    /**
     * @test
     */
    public function it_doesnt_create_text_from_short_string()
    {
        $this->expectException(InvalidText::class);
        $this->expectExceptionMessage(
            'The todo text is invalid: Value "a" is too short, it should have at least 3 characters, but only has 1 characters.'
        );
        TodoText::fromString('a');
    }

    /**
     * @test
     * @depends it_creates_text_from_string
     */
    public function it_can_be_compared()
    {
        $first = TodoText::fromString('foo');
        $second = TodoText::fromString('foo');
        $third = TodoText::fromString('bar');
        $fourth = new class() implements ValueObject {
            public function sameValueAs(ValueObject $object): bool
            {
                return false;
            }
        };

        $this->assertTrue($first->sameValueAs($second));
        $this->assertFalse($first->sameValueAs($third));
        $this->assertFalse($first->sameValueAs($fourth));
    }
}

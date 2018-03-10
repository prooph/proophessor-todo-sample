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

namespace ProophTest\ProophessorDo\Model\User;

use Prooph\ProophessorDo\Model\User\Exception\InvalidName;
use Prooph\ProophessorDo\Model\User\UserName;
use Prooph\ProophessorDo\Model\ValueObject;
use ProophTest\ProophessorDo\TestCase;

class UserNameTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_name_from_string()
    {
        $name = UserName::fromString('foo');
        $this->assertSame('foo', $name->toString());
    }

    /**
     * @test
     */
    public function it_doesnt_create_name_from_empty_string()
    {
        $this->expectException(InvalidName::class);
        $this->expectExceptionMessage(
            'Invalid user name because Value "" is empty, but non empty value was expected.'
        );
        UserName::fromString('');
    }

    /**
     * @test
     * @depends it_creates_name_from_string
     */
    public function it_can_be_compared()
    {
        $first = UserName::fromString('foo');
        $second = UserName::fromString('foo');
        $third = UserName::fromString('bar');
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

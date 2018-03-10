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

namespace Prooph\ProophessorDo\Model\User;

use Prooph\ProophessorDo\Model\ValueObject;
use Zend\Validator\EmailAddress as EmailAddressValidator;

final class EmailAddress implements ValueObject
{
    /**
     * @var string
     */
    private $email;

    public static function fromString(string $email): EmailAddress
    {
        $validator = new EmailAddressValidator();

        if (! $validator->isValid($email)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        return new self($email);
    }

    private function __construct(string $email)
    {
        $this->email = $email;
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function sameValueAs(ValueObject $other): bool
    {
        return get_class($this) === get_class($other) && $this->toString() === $other->toString();
    }
}

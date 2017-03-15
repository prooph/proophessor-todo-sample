<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prooph\ProophessorDo\Model\Todo;

use Prooph\ProophessorDo\Model\Enum;

/**
 * Class TodoReminderStatus
 *
 * @package Prooph\ProophessorDo\Model\Todo
 * @author Roman Sachse <r.sachse@ipark-media.de>
 *
 * @method static TodoReminderStatus OPEN()
 * @method static TodoReminderStatus CLOSED()
 */
final class TodoReminderStatus extends Enum
{
    const OPEN = "OPEN";
    const CLOSED = "CLOSED";
}

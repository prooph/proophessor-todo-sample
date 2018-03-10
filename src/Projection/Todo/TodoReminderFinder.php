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

namespace Prooph\ProophessorDo\Projection\Todo;

use Doctrine\DBAL\Connection;
use Prooph\ProophessorDo\Model\Todo\TodoReminderStatus;
use Prooph\ProophessorDo\Projection\Table;

class TodoReminderFinder
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findOpen(): array
    {
        $stmt = $this->connection->prepare(
            sprintf(
                "SELECT * FROM %s where reminder < NOW() AND status = '%s'",
                Table::TODO_REMINDER,
                TodoReminderStatus::OPEN
            )
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

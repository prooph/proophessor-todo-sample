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

namespace Prooph\ProophessorDo\Projection\User;

use Doctrine\DBAL\Connection;
use Prooph\EventStore\Projection\AbstractReadModel;
use Prooph\ProophessorDo\Projection\Table;

final class UserReadModel extends AbstractReadModel
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $tableName = Table::USER;

        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `open_todos` int(11) NOT NULL DEFAULT '0',
  `done_todos` int(11) NOT NULL DEFAULT '0',
  `expired_todos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOT;

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function isInitialized(): bool
    {
        $tableName = Table::USER;

        $sql = "SHOW TABLES LIKE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        $result = $statement->fetch();

        if (false === $result) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $tableName = Table::USER;

        $sql = "TRUNCATE TABLE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableName = Table::USER;

        $sql = "DROP TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::USER, $data);
    }

    protected function postTodo(string $assigneeId): void
    {
        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $assigneeId);

        $stmt->execute();
    }

    protected function markTodoAsDone(string $assigneeId): void
    {
        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos - 1, done_todos = done_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $assigneeId);

        $stmt->execute();
    }

    protected function reopenTodo(string $assigneeId): void
    {
        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1, done_todos = done_todos - 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $assigneeId);

        $stmt->execute();
    }

    protected function markTodoAsExpired(string $assigneeId): void
    {
        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos - 1, expired_todos = expired_todos + 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $assigneeId);

        $stmt->execute();
    }

    protected function unmarkedTodoAsExpired(string $assigneeId): void
    {
        $stmt = $this->connection->prepare(sprintf('UPDATE %s SET open_todos = open_todos + 1, expired_todos = expired_todos - 1 WHERE id = :assignee_id', Table::USER));

        $stmt->bindValue('assignee_id', $assigneeId);

        $stmt->execute();
    }
}

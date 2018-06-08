<?php

namespace Rednose\FrameworkBundle\Test;

use Doctrine\DBAL\Connection;

class ORMPurger
{
    /**
     * List of tables to ignore
     *
     * @var array
     */
    protected $exclude = [];

    /**
     * Purge the database this connection uses
     *
     * @param Connection $connection
     */
    public function purge(Connection $connection)
    {
        if($connection->getDatabasePlatform()->getName() !== 'mysql') {
            throw new \Exceptiom('ORMPurger exception: unsupported database detected');
        }

        $connection->executeUpdate("SET foreign_key_checks = 0;");

        $this->purgeTables(
            $this->findNonEmptyTables($connection),
            $connection
        );

        $connection->executeUpdate("SET foreign_key_checks = 1;");
    }

    /**
     * Do not purge this table
     *
     * @param string $exclude
     */
    public function addExclude(string $exclude)
    {
        $this->exclude[] = $exclude;
    }

    /**
     * Purge tables
     *
     * @param            $tables
     * @param Connection $connection
     */
    private function purgeTables($tables, Connection $connection)
    {
        foreach ($tables as $table) {
            $connection->executeUpdate('DELETE FROM `' . $table .  '`');
        }
    }

    /**
     * Count all records in the database to get a list of tables that
     * must be purged.
     *
     * @param Connection $connection
     *
     * @return array
     */
    private function findNonEmptyTables(Connection $connection): array
    {
        $countQuery = 'SELECT null';
        $allTables  = $connection->fetchAll('SHOW TABLES');

        foreach ($allTables as $table) {
            $table = current($table);

            if (array_search($table, $this->exclude, true) === false) {
                $countQuery .= ', (SELECT COUNT(*) FROM `' . $table.  '` WHERE 1) as `' . $table . "`";
            }
        }

        $tables = $connection->fetchAssoc($countQuery);
        $tables = array_filter($tables, function($input) { return $input > 0; });

        return array_keys($tables);
    }
}
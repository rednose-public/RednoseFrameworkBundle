<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Redis;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;

/**
 * Redis maintenance task class processor
 */
class RedisMaintenance
{
    const MUTEX_TABLE_NAME = 'rednose_framework_redis_maintenance_mutex';

    /**
     * @var RedisFactory
     */
    protected $factory;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * RedisMaintenance constructor
     *
     * @param RedisFactory $factory
     */
    public function __construct(RedisFactory $factory, Connection $connection)
    {
        $this->factory    = $factory;
        $this->connection = $connection;
    }

    /**
     * Process all task classes.
     *
     * If a class is runOnce it will be skipped if its already marked as executed
     *
     * @throws \Exception
     * @param array $files
     *
     * @return integer
     */
    public function process(array $files)
    {
        $count = 0;

        if ($this->factory->isConfigured() === false) {
            throw new \Exception('Redis is not configured');
        }

        foreach ($files as $file) {
            $taskClass = $this->loadClass($file);

            if ($taskClass->runOnce() === false) {
                $taskClass->up($this->factory);

                $count++;

                continue;
            }

            if ($this->checkMutex($this->getClassName($file, false)) === false) {
                $taskClass->up($this->factory);

                $count++;

                $this->createMutex($this->getClassName($file, false));

                continue;
            }
        }

        return $count;
    }

    /**
     * Load the task class
     *
     * @param string $file
     *
     * @return RedisMaintenanceTaskInterface
     * @throws \Exception
     */
    private function loadClass(string $file) : RedisMaintenanceTaskInterface
    {
        $className = $this->getClassName($file);

        if (class_exists($className) === false) {
            require($file);
        }

        $class = new $className();

        return $class;
    }

    /**
     * Mark class as executed
     *
     * @param string $className
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createMutex(string $className)
    {
        $this->connection->executeUpdate(
            'INSERT INTO ' . self::MUTEX_TABLE_NAME . ' set version = \'' . $className .'\''
        );
    }

    private function checkMutex(string $className) : bool
    {
        if ($this->mutexTableExists() === false) {
            $this->createMutexTable();
        }

        $exists = $this->connection->fetchColumn('SELECT version FROM ' . self::MUTEX_TABLE_NAME . ' WHERE version = \'' . $className .'\'');

        return is_string($exists);
    }

    /**
     * Read a file and try to determine its namespace
     *
     * @param string $file
     *
     * @return string
     * @throws \Exception
     */
    private function extractNamespace(string $file) : string
    {
        $nameSpaceBuffer = [];
        $content         = file_get_contents($file);

        preg_match('/namespace (.*);/i', $content, $nameSpaceBuffer);

        if (count($nameSpaceBuffer) === 2) {
            return trim($nameSpaceBuffer[1]);
        }

        throw new \Exception('Unable to determine namespace of class ' . $file);
    }

    /**
     * Query the mutex table to determine if it exists
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function mutexTableExists() : bool
    {
        try {
            $this->connection->fetchColumn('SELECT version FROM ' . self::MUTEX_TABLE_NAME . '');
        } catch (TableNotFoundException $e) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * Create a mutex table
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createMutexTable()
    {
        $tableBuilder = new Schema();
        $connection   = $this->connection;

        $tblMutex = $tableBuilder->createTable(self::MUTEX_TABLE_NAME);
        $tblMutex->addColumn('version', 'text', ['length' => 16]);

        $sql = $tableBuilder->toSQL($connection->getDatabasePlatform());

        foreach ($sql as $sqlInstruction) {
            $connection->exec($sqlInstruction);
        }
    }

    /**
     * Resolve className from file path
     *
     * @param string $file
     * @param bool   $withNamespace
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getClassName(string $file, bool $withNamespace = true) : string
    {
        $file = realpath($file);

        $nameSpace = $this->extractNamespace($file);

        $className = substr($file, strrpos($file, '/'));
        $className = str_replace(['/', '.phps', '.php'], '', $className);

        if ($withNamespace === true) {
            $className = '\\' . $nameSpace . '\\' . $className;
        }

        return $className;
    }

}
<?php

declare(strict_types = 1);

namespace KFencer\Db\TransactionManager;

use KFencer\Db\TransactionManager\Exception\DbExceptionInterface;
use KFencer\Db\TransactionManager\Exception\DbQueryExceptionInterface;
use KFencer\Db\TransactionManager\Exception\DbTransactionExceptionInterface;

interface TransactionManagerInterface
{
    /**
     * @throws DbExceptionInterface
     */
    public function setSessionTransactionLevel(TransactionLevel $transactionLevel): void;

    public function beginTransaction(TransactionLevel $transactionLevel = null, bool $readOnly = false): void;

    public function inTransaction(): bool;

    public function getNestedTransactionLevel(): ?int;

    public function getSessionTransactionLevel(): TransactionLevel;

    public function getTransactionLevel(): TransactionLevel;

    /**
     * @throws DbQueryExceptionInterface
     */
    public function query(string $sql): \PDOStatement;

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function addDependency(string $uniqKey, \Closure $dependency): void;

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function commit(): void;

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function rollback(): void;
}

<?php

declare(strict_types = 1);

namespace KFencer\Db\TransactionManager;

use KFencer\Db\TransactionManager\Exception\DbExceptionInterface;
use KFencer\Db\TransactionManager\Exception\DbQueryExceptionInterface;
use KFencer\Db\TransactionManager\Exception\DbTransactionExceptionInterface;
use KFencer\Db\TransactionManager\Exception\Transaction\HasNoTransactionException;
use KFencer\Db\TransactionManager\Platform\PlatformInterface;

class TransactionManager implements TransactionManagerInterface
{
    protected ?int $nestedTransactionLevel = null;

    /** @var \Closure[] */
    protected array $dependencies;

    public function __construct(
        protected PlatformInterface $platform
    ) {}

    /**
     * @throws DbExceptionInterface
     */
    public function setSessionTransactionLevel(TransactionLevel $transactionLevel): void
    {
        // TODO: Implement setSessionTransactionLevel() method.
    }

    public function beginTransaction(TransactionLevel $transactionLevel = null, bool $readOnly = false): void
    {
        if ($this->nestedTransactionLevel === null) {
            $this->query($this->platform->getBeginTransactionStatement($transactionLevel, $readOnly));
            $this->nestedTransactionLevel = 0;
        } else {
            $nestedTransactionLevel = $this->nestedTransactionLevel + 1;

            $this->query(
                $this->platform->getSavepointStatement(
                    $this->getNestedTransactionPointName($nestedTransactionLevel)
                )
            );

            $this->nestedTransactionLevel = $nestedTransactionLevel;
        }
    }

    public function inTransaction(): bool
    {
        return $this->nestedTransactionLevel !== null;
    }

    public function getNestedTransactionLevel(): ?int
    {
        return $this->nestedTransactionLevel;
    }

    public function getSessionTransactionLevel(): TransactionLevel
    {
        // TODO: Implement getSessionTransactionLevel() method.
    }

    public function getTransactionLevel(): TransactionLevel
    {
        // TODO: Implement getTransactionLevel() method.
    }

    /**
     * @throws DbQueryExceptionInterface
     */
    public function query(string $sql): \PDOStatement
    {
        // TODO: Implement execute() method.
    }

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function addDependency(string $uniqKey, \Closure $dependency): void
    {
        $this->dependencies[$uniqKey] = $dependency;
    }

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function commit(): void
    {
        if ($this->nestedTransactionLevel === null) {
            throw new HasNoTransactionException();
        }

        if ($this->nestedTransactionLevel === 0) {
            $this->query(
                $this->platform->getCommitStatement()
            );

            foreach ($this->dependencies as $dependency) {
                $dependency();
            }

            $this->dependencies = [];

            $this->nestedTransactionLevel = null;
        } else {
            $checkPoint = $this->getNestedTransactionPointName(
                $this->nestedTransactionLevel
            );

            $this->query(
                $this->platform->getReleaseSavepointStatement($checkPoint)
            );

            $this->nestedTransactionLevel--;
        }
    }

    /**
     * @throws DbTransactionExceptionInterface
     */
    public function rollback(): void
    {
        if ($this->nestedTransactionLevel === null) {
            throw new HasNoTransactionException();
        }

        if ($this->nestedTransactionLevel === 0) {
            $this->query(
                $this->platform->getRollbackStatement()
            );

            $this->nestedTransactionLevel = null;
        } else {
            $checkPoint = $this->getNestedTransactionPointName(
                $this->nestedTransactionLevel
            );

            $this->query(
                $this->platform->getRollbackToSavepointStatement($checkPoint)
            );

            $this->nestedTransactionLevel--;
        }
    }

    protected function getNestedTransactionPointName(int $nestedTransactionLevel): string
    {
        return $this->platform->getNestedTransactionPointPrefix() . $nestedTransactionLevel;
    }
}

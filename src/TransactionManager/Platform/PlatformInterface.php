<?php

declare(strict_types = 1);

namespace KFencer\Db\TransactionManager\Platform;

use KFencer\Db\TransactionManager\TransactionLevel;

interface PlatformInterface
{
    public function getTransactionLevelName(TransactionLevel $transactionLevel): string;
    public function getBeginTransactionStatement(TransactionLevel $transactionLevel = null, bool $readOnly = false): string;
    public function getNestedTransactionPointPrefix(): string;
    public function getSavepointStatement(string $name): string;
    public function getRollbackToSavepointStatement(string $name): string;
    public function getReleaseSavepointStatement(string $name): string;
    public function getCommitStatement(): string;
    public function getRollbackStatement(): string;
}
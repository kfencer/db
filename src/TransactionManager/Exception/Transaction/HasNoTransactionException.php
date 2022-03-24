<?php

declare(strict_types = 1);

namespace KFencer\Db\TransactionManager\Exception\Transaction;

use KFencer\Db\TransactionManager\Exception\DbTransactionExceptionInterface;

class HasNoTransactionException extends \Exception implements DbTransactionExceptionInterface
{

}

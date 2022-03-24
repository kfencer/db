<?php

declare(strict_types = 1);

namespace KFencer\Db\TransactionManager;

enum TransactionLevel
{
    case ReadUncommited;
    case ReadCommited;
    case RepeatableRead;
    case Serializable;
}
<?php

namespace App\Exceptions\Wallet;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(string $message = "Saldo member tidak mencukupi untuk melakukan transaksi ini.")
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Exceptions\Wallet;

use Exception;

class DuplicateTransactionException extends Exception
{
    public function __construct(string $message = "Transaksi dengan kode referensi ini sudah diproses sebelumnya.")
    {
        parent::__construct($message);
    }
}

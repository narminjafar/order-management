<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use RuntimeException;

trait DBTransaction
{
    /**
     * Executes a callable function within a database transaction.
     *
     * @param callable $callback The function to execute
     * @param string $errorMessage Error message if transaction fails
     * @return mixed The result of the callable function
     * @throws RuntimeException if transaction fails
     */

    public function runInTransaction(callable $callback, string $errorMessage,int $errorCode)
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            throw new RuntimeException($errorMessage . $e->getMessage(), $errorCode ?? $e->getCode(), $e);
        }
    }
}

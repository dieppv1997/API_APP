<?php

namespace App\Database;

use Illuminate\Database\MySqlConnection as BaseMysqlConnection;

class StickySqlConnection extends BaseMysqlConnection
{
    public function recordsHaveBeenModified($value = true)
    {
        session(['force_pdo_write_until' => time() + 1]);
        parent::recordsHaveBeenModified($value);
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        if (time() < session('force_pdo_write_until')) {
            return parent::select($query, $bindings, false);
        }
        return parent::select($query, $bindings, $useReadPdo);
    }
}

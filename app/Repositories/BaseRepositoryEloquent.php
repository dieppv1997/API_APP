<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

class BaseRepositoryEloquent extends BaseRepository
{

    public function model()
    {
        //
    }

    public function insert(array $data): bool
    {
        return $this->model->query()->insert($data);
    }

    public function upsert(array $data, $uniqueBy, $updateColumns): bool
    {
        return $this->model->query()->upsert($data, $uniqueBy, $updateColumns);
    }
}

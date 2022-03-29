<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services;

use Prettus\Repository\Contracts\RepositoryInterface;

class BaseService
{
    protected $showField = ['*'];
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    private function setShowField(&$columns)
    {
        if ($columns === null) {
            $columns = $this->showField;
        }
    }

    public function all($columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->all($columns);
    }

    public function first($columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->first($columns);
    }

    public function paginate($limit = null, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->paginate($limit, $columns);
    }

    public function find($id, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->find($id, $columns);
    }

    public function findByField($field, $value, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->findByField($field, $value, $columns);
    }
    public function findWhere(array $where, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->findWhere($where, $columns);
    }

    public function findWhereIn($field, array $where, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->findWhereIn($field, $where, $columns);
    }

    public function findWhereNotIn($field, array $where, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->findWhereNotIn($field, $where, $columns);
    }

    public function findWhereBetween($field, array $where, $columns = null)
    {
        $this->setShowField($columns);
        return $this->repository->findWhereBetween($field, $where, $columns);
    }

    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    public function update(array $attributes, $id)
    {
        return $this->repository->update($attributes, $id);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function deleteWhere(array $where)
    {
        return $this->repository->deleteWhere($where);
    }

    public function orderBy($column, $direction = 'asc')
    {
        return $this->repository->orderBy($column, $direction);
    }

    public function with(array $relations)
    {
        return $this->repository->with($relations);
    }
}

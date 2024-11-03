<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = [], int $perPage = null, ?int $page = null)
    {
        $query = $this->model;

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $query = $query->where($field, $value);
            }
        }

        return $perPage ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public function getBy(array $filters =[])
    {
        $query = $this->model;

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $query = $query->where($field, $value);
            }
        }

        return $query->get();
    }


    public function updateOrCreate(array $filters = [], array $data = [])
    {
        $queryFilters = array_filter($filters, fn($value) => !is_null($value));

        return $this->model->updateOrCreate($queryFilters, $data);
    }

    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        return $record ? $record->update($data) : false;
    }


    public function delete(int $id): bool
    {
        $record = $this->find($id);

        return $record ? $record->delete() : false;
    }

    public function deleteByOrderIdAndProductId(int $orderId, int $productId){

    }

}

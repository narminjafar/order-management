<?php

namespace App\Interfaces\Repository;

use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryInterface
{
    public function all(array $filters = [], int $perPage = null, ?int $page = null);
    public function getBy(array $filters = []);
    public function updateOrCreate(array $filters = [], array $data = []);
    public function find(int $id, array $relations = []): ?Model;

    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
interface ICrudable

{

    public function create(object $request): array;

    public function update(int $id, array $data): ?array;

    public function delete(int $id): void;

    public function findModelById(int $id): ?Model;

    public function findAllModels(int $perPage = 10): ?LengthAwarePaginator;

    public function buildModelInstance(object $modelData, array $exceptCollection):Model;

    public function setFormatResponse(Model $model): array;


}
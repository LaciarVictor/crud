<?php

namespace App\Services;

use App\Interfaces\ICrudable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
* Clase abstracta que define los metodos comunes para las operaciones CRUD.
 */
abstract class CrudService implements ICrudable
{
    /**
     * El modelo que se va a utilizar.
     *
     * @var Model
     */
    protected $model;

    /**
     * Crea una nueva instancia de CrudService.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Crea una nueva instancia del modelo y la guarda en la base de datos.
     *
     * @param object $request
     * @return Model|null
     */
    public function create(object $request): ?Model
    {
        $modelData = $request->validated();
        $model = $this->model->create($modelData);

        return $model;
    }

    /**
     * Actualiza la instancia del modelo y la guarda en la base de datos.
     *
     * @param integer $id
     * @param object $request
     * @return Model|null
     */
    public function update(int $id, object $request): ?Model
    {
        $model = $this->findModelById($id);
        $validationRequest = $request->validated();

        if ($model) {
            $model->fill($validationRequest);
            $model->save();

            return $model;
        }

        return null;
    }

    /**
     * Elimina la instancia del modelo de la base de datos.
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): bool
    {
        return $this->findModelById($id)->delete();
    }
    

    /**
     * Busca la instancia del modelo por su id.
     *
     * @param integer $id
     * @return Model|null
     */
    public function findModelById(int $id): ?Model
    {
        return $this->model->find($id);

    }




    /**
     * Devuelve todos los registros del modelo paginados. Por defecto devuelve 10 registros.
     * 
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function findAllModels(int $perPage = 10): ? LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }





    /**
     * Especifica el formato JSON personalizado en el que se devolverá el modelo.
     *
     * @param Model $model
     * @return array
     */
    abstract public function setJSONResponse(Model $model): array;
}

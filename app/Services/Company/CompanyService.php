<?php

namespace App\Services\Company;

use App\Interfaces\ICrudable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\CrudService;
use App\Models\Company;

/**
 * Operaciones generales CRUD.
 */
abstract class CompanyService extends CrudService implements ICrudable
{
    /**
     * El modelo principal.
     *
     * @var Company
     */
    protected $company;




    /**
     * Recibe el modelo que utilizará el crud.
     *
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }



    /**
     * Crea una nueva instancia del modelo,
     * lo guarda en la base de datos,
     * y devuelve el modelo creado.
     *
     * @param object $request
     * @return array
     */
    public function create(object $request): array
    {
        $modelData = $request->validated();

        $model = $this->model->create($modelData);

        $model->save();

        return $this->setFormatResponse($model);
    }




    /**
     * Devuelve la instancia del modelo,
     * aplica las actualizaciones,
     * y las guarda en la base de datos.
     *
     * @param integer $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): ?array
    {
        $model = $this->findModelById($id);

        if($model)
        {
            $model->fill($data);

            $model->save();   
            return $this->setFormatResponse($model);

        }
            return null;

    }




    /**
     * Busca la instancia del modelo de la base de datos.
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        $model = $this->findModelById($id);
        $model->delete();
    }




    /**
     * Busca un modelo por su id.
     *
     * @param integer $id
     * @return Model|null
     */
    public function findModelById(int $id): ?array
    {

        $m = $this->model->findOrFail($id);
        if($m)
        {
            return $m;

        }
        return null;


    }




    /**
     * Devuelve todos los registros del modelo.
     *
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function findAllModels(int $perPage = 10): ?LengthAwarePaginator
    {
        $modelsPaginator = $this->model->paginate($perPage);

        $models = collect($modelsPaginator->items());

        $formattedModels = $models->map(function ($model) {
            return $this->setFormatResponse($model);
        });

        $paginator = new LengthAwarePaginator(
            $formattedModels,
            $modelsPaginator->total(),
            $modelsPaginator->perPage(),
            $modelsPaginator->currentPage(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );

        return $paginator;
    }




    /**
     * Especifica el formato personalizado en el que se devolverá el modelo.
     *
     * @param Model $model
     * @return array
     */
    abstract public function setFormatResponse(Model $model): array;
}

<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
interface ICrudable

{
    /**
     * Crea una nueva instancia del modelo y la guarda en la base de datos.
     * 
     * @param object $request
     * @return Model|null
     */
    public function create(object $request): ?Model;



    /**
     * Busca un modelo por su número identificador en la base de datos.
     *
     * @param integer $id
     * @return Model|null
     */
    public function findModelById(int $id): ?Model;


    /**
     * Busca todos los modelos existentes en la base de datos.
     * Si hay datos, devuelve el resultado paginado.
     *
     * @param integer $perPage
     * @return LengthAwarePaginator|null
     */
    public function findAllModels(int $perPage = 10): ?LengthAwarePaginator;



    /**
     * Actualiza la instancia del modelo y la guarda en la base de datos.
     *
     * @param integer $id
     * @param object $request
     * @return Model|null
     */
    public function update(int $id, object $request): ?Model;




    /**
     * Elimina la instancia del modelo en la base de datos.
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): bool;



}
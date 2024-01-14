<?php

namespace App\Services\Company;

use App\Interfaces\ICrudable;

use App\Services\CrudService;

use App\Models\Company;

use App\Http\Requests\CompanyRequests\CompanyCreateRequest;
use App\Http\Requests\CompanyRequests\CompanyUpdateRequest;

use DateTime;

use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Pagination\Paginator;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Throwable;



class CompanyService extends CrudService implements ICrudable
{
    
    protected $customLengthAwarePaginator;

    public function __construct(Company $company)
    {
        parent::__construct($company);

    }




    /**
     * Crea una nueva empresa.
     * 
     * @param CompanyCreateRequest $request
     * @return JsonResponse
     */
    public function companyCreate(CompanyCreateRequest $request): JsonResponse
    {
        try {

            $company = parent::create($request);

            return response()->json([$this->setJSONResponse($company)]);
        } catch (ValidationException $ex) {


            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }





    /**
     * Actualiza la empresa.
     *
     * @param CompanyUpdateRequest $request
     * @return JsonResponse
     */
    public function CompanyUpdate(CompanyUpdateRequest $request, int $id): JsonResponse
    {
        try {



            $company = parent::update($id, $request);



            if ($company) {
                return response()->json($this->setJSONResponse($company));
            } else {
                throw new ModelNotFoundException('No se encontró el usuario');
            }
        } catch (ModelNotFoundException $ex) {

            return response()->json(['message' => $ex->getMessage()], 404);
        } catch (ValidationException $ex) {

            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }




/**
 * Borra la empresa.
 *
 * @param integer $id
 * @return JsonResponse
 */
    public function deleteCompany(int $id): JsonResponse
    {
        try {
            $success = parent::delete($id);

            return response()->json(['message' => $success ? 'Empresa eliminada correctamente.' : 'No se encontró la empresa.'], $success ? 200 : 404);
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }



    /**
     * Busca todas las empresas.
     *
     * @param int $perPage El número de usuarios por página (por defecto: 10)
     * @return  LengthAwarePaginator
     * @return JsonResponse
     * @throws \Exception Si ocurre un error durante la ejecución de la función.
     */
    public function findAllCompanies($perPage = 10): LengthAwarePaginator | JsonResponse
    {
        try {
            //Busca todos los usuarios y los pagina
            $companiesPaginator = $this->model->paginate($perPage);

            if ($companiesPaginator->isEmpty()) {
                return response()->json(['message' => 'No hay empresas registradas.']);
            }
            // Formatea los usuarios para que el rol aparezca en la misma llave.
            $formattedCompany = collect($companiesPaginator->items())->map(function ($company) {
                return $this->setJSONResponse($company);
            });

            //Agrega la paginación. El total de páginas, la petición actual 
            //la petición anterior, la petición siguiente y la petición final.
            $paginator = new LengthAwarePaginator(
                $formattedCompany,
                $companiesPaginator->total(),
                $companiesPaginator->perPage(),
                $companiesPaginator->currentPage(),
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => request()->query(),
                ]
            );

            return $paginator;
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }



    /**
     * Busca una empresa por su id.
     *
     * @param integer $userId
     * @return JsonResponse
     */
    public function findCompany(int $companyId): JsonResponse
    {
        try {
            $company = $this->model->findOrFail($companyId);
            return response()->json($this->setJSONResponse($company));
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }



/**
 * Establece el formato JSON de la respuesta.
 *
 * @param Company $companyData
 * @return array
 */
    public function setJSONResponse($companyData): array
    {
        $company = $companyData;

        return [
            'id' => $company->id,
            'company_name' => $company->name,
            'legal_name' => $company->legal_name,
            'cuit' => $company->tax_id,
            'street' => $company->address,
            'street_no' => $company->street_no,
            'city' => $company->city,
            'state' => $company->state,
            'country' => $company->country,
            'postal_code' => $company->postal_code,
            'phone_number' => $company->phone_number,
            'email' => $company->email,
            'industry' => $company->industry,
            'parent_company_id' => $company->parent_company_id,
            'registration_date' => $company->registration_date,
            'financial_revenue' => $company->financial_revenue,
            'created_at' => $this->formattedDate($company->created_at),
            'updated_at' => $this->formattedDate($company->updated_at),
        ];
    }


/**
 * Cambia el formato de la fecha a dd/mm/yyyy.
 *
 * @param string $timestamp
 * @return string
 */
    function formattedDate($timestamp): string
    {
        // Crear un objeto DateTime a partir del timestamp
        $date = new DateTime($timestamp);

        // Reformatear la fecha al formato dd:mm:yyyy
        return $date->format('d/m/Y');
    }



}

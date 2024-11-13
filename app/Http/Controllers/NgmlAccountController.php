<?php

namespace App\Http\Controllers;

use App\Http\Resources\NgmlAccountResource;
use App\Services\NgmlAccountService;
use Illuminate\Http\Request;

class NgmlAccountController extends Controller
{
    /**
     * The NgmlAccountService instance.
     *
     * @var NgmlAccountService
     */
    protected NgmlAccountService $ngmlAccountService;

    /**
     * NgmlAccountController constructor.
     *
     * @param NgmlAccountService $ngmlAccountService
     */
    public function __construct(NgmlAccountService $ngmlAccountService)
    {
        $this->ngmlAccountService = $ngmlAccountService;
    }

    /**
     * @OA\Get(
     *     path="/api/ngml-accounts",
     *     tags={"NGML Accounts"},
     *     summary="Get a list of NGML accounts with filters and pagination",
     *     description="Fetches a list of NGML accounts, with optional filtering and pagination support.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", example=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/ngml-accounts?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/NgmlAccount")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->all();
            $per_page = $request->input('per_page', 50);

            $ngmlAccounts = $this->ngmlAccountService->getAllWithFilters($filters, $per_page);

            return NgmlAccountResource::collection($ngmlAccounts)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $ngmlAccounts->currentPage(),
                        'next_page_url' => $ngmlAccounts->nextPageUrl(),
                        'prev_page_url' => $ngmlAccounts->previousPageUrl(),
                        'per_page' => $ngmlAccounts->perPage(),
                        'total' => $ngmlAccounts->total(),
                        'last_page' => $ngmlAccounts->lastPage(),
                    ]
                ])
                ->response()
                ->setStatusCode(200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get details of a specific NGML account by ID.
     *
     * @OA\Get(
     *     path="/api/ngml-accounts/{id}",
     *     tags={"NGML Accounts"},
     *     summary="Get details of a specific NGML account",
     *     description="Fetches details of a specific NGML account by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the NGML account",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/NgmlAccount")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $ngmlAccount = $this->ngmlAccountService->getById($id);

            return (new NgmlAccountResource($ngmlAccount))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'NGML account not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific NGML account by ID.
     *
     * @OA\Delete(
     *     path="/api/ngml-accounts/{id}",
     *     tags={"NGML Accounts"},
     *     summary="Delete a specific NGML account",
     *     description="Deletes a specific NGML account by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the NGML account",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="NGML account deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $ngmlAccount = $this->ngmlAccountService->getById($id);
            $ngmlAccount->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'NGML account deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'NGML account not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

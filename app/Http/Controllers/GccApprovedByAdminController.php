<?php

namespace App\Http\Controllers;

use App\Http\Resources\GccApprovedByAdminResource;
use App\Services\GccApprovedByAdminService;
use Illuminate\Http\Request;

class GccApprovedByAdminController extends Controller
{
    /**
     * The GccApprovedByAdminService instance.
     *
     * @var GccApprovedByAdminService
     */
    protected GccApprovedByAdminService $gccApprovedByAdminService;

    /**
     * GccApprovedByAdminController constructor.
     *
     * @param GccApprovedByAdminService $gccApprovedByAdminService
     */
    public function __construct(GccApprovedByAdminService $gccApprovedByAdminService)
    {
        $this->gccApprovedByAdminService = $gccApprovedByAdminService;
    }

    /**
     * @OA\Get(
     *     path="/api/gcc-approved-by-admins",
     *     tags={"GCC Approved By Admins"},
     *     summary="Get a list of GCC approved records with filters and pagination",
     *     description="Fetches a list of GCC approved records, with optional filtering based on fields in the gcc_approved_by_admins table, and supports pagination.",
     *
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
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter by start date for date",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter by end date for date",
     *         @OA\Schema(type="string", format="date-time")
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/gcc-approved-by-admins?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GccApprovedByAdmin")
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

            $gccApprovedRecords = $this->gccApprovedByAdminService->getAllWithFilters($filters, $per_page);

            return GccApprovedByAdminResource::collection($gccApprovedRecords)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $gccApprovedRecords->currentPage(),
                        'next_page_url' => $gccApprovedRecords->nextPageUrl(),
                        'prev_page_url' => $gccApprovedRecords->previousPageUrl(),
                        'per_page' => $gccApprovedRecords->perPage(),
                        'total' => $gccApprovedRecords->total(),
                        'last_page' => $gccApprovedRecords->lastPage(),
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
     * Get details of a specific GCC approved record by ID.
     *
     * @OA\Get(
     *     path="/api/gcc-approved-by-admins/{id}",
     *     tags={"GCC Approved By Admins"},
     *     summary="Get details of a specific GCC approved record",
     *     description="Fetches details of a specific GCC approved record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the GCC approved record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/GccApprovedByAdmin")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the GCC approved record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $gccApprovedRecord = $this->gccApprovedByAdminService->getById($id);

            return (new GccApprovedByAdminResource($gccApprovedRecord))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'GCC Approved record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific GCC approved record by ID.
     *
     * @OA\Delete(
     *     path="/api/gcc-approved-by-admins/{id}",
     *     tags={"GCC Approved By Admins"},
     *     summary="Delete a specific GCC approved record",
     *     description="Deletes a specific GCC approved record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the GCC approved record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="GCC approved record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the GCC approved record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $gccApprovedRecord = $this->gccApprovedByAdminService->getById($id);
            $gccApprovedRecord->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'GCC approved record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'GCC Approved record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

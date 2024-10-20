<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceAdviceApprovedByResource;
use App\Services\InvoiceAdviceApprovedByService;
use Illuminate\Http\Request;

class InvoiceAdviceApprovedByController extends Controller
{
    /**
     * The InvoiceAdviceApprovedByService instance.
     *
     * @var InvoiceAdviceApprovedByService
     */
    protected InvoiceAdviceApprovedByService $invoiceAdviceApprovedByService;

    /**
     * InvoiceAdviceApprovedByController constructor.
     *
     * @param InvoiceAdviceApprovedByService $invoiceAdviceApprovedByService
     */
    public function __construct(InvoiceAdviceApprovedByService $invoiceAdviceApprovedByService)
    {
        $this->invoiceAdviceApprovedByService = $invoiceAdviceApprovedByService;
    }

    /**
     * @OA\Get(
     *     path="/api/invoice-advice-approved-bies",
     *     tags={"Invoice Advice Approved Bies"},
     *     summary="Get a list of approved invoice advices with filters and pagination",
     *     description="Fetches a list of approved invoice advices, with optional filtering based on fields in the invoice advice approved bies table, and supports pagination.",
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
     *         description="Filter by start date",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter by end date",
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/invoice-advice-approved-bies?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/InvoiceAdviceApprovedBy")
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
            $per_page = $request->input('per_page', default: 50);

            $approvedBies = $this->invoiceAdviceApprovedByService->getAllWithFilters($filters, $per_page);

            return InvoiceAdviceApprovedByResource::collection($approvedBies)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $approvedBies->currentPage(),
                        'next_page_url' => $approvedBies->nextPageUrl(),
                        'prev_page_url' => $approvedBies->previousPageUrl(),
                        'per_page' => $approvedBies->perPage(),
                        'total' => $approvedBies->total(),
                        'last_page' => $approvedBies->lastPage(),
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
     * Get details of a specific InvoiceAdviceApprovedBy record by ID.
     *
     * @OA\Get(
     *     path="/api/invoice-advice-approved-bies/{id}",
     *     tags={"Invoice Advice Approved Bies"},
     *     summary="Get details of a specific InvoiceAdviceApprovedBy record",
     *     description="Fetches details of a specific InvoiceAdviceApprovedBy record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdviceApprovedBy record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/InvoiceAdviceApprovedBy")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the InvoiceAdviceApprovedBy record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $approvedBy = $this->invoiceAdviceApprovedByService->getById($id);

            return (new InvoiceAdviceApprovedByResource($approvedBy))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Advice Approved By record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific InvoiceAdviceApprovedBy record by ID.
     *
     * @OA\Delete(
     *     path="/api/invoice-advice-approved-bies/{id}",
     *     tags={"Invoice Advice Approved Bies"},
     *     summary="Delete a specific InvoiceAdviceApprovedBy record",
     *     description="Deletes a specific InvoiceAdviceApprovedBy record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdviceApprovedBy record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice Advice Approved By record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the InvoiceAdviceApprovedBy record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $approvedBy = $this->invoiceAdviceApprovedByService->getById($id);
            $approvedBy->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice Advice Approved By record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Advice Approved By record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

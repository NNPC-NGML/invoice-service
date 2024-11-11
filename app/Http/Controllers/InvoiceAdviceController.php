<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceAdviceResource;
use App\Models\Invoice;
use App\Models\InvoiceAdvice;
use App\Services\InvoiceAdviceService;
use Illuminate\Http\Request;
use Skillz\Nnpcreusable\Models\CustomerSite;

class InvoiceAdviceController extends Controller
{
    /**
     * The InvoiceAdviceService instance.
     *
     * @var InvoiceAdviceService
     */
    protected InvoiceAdviceService $invoiceAdviceService;

    /**
     * InvoiceAdviceController constructor.
     *
     * @param InvoiceAdviceService $invoiceAdviceService
     */
    public function __construct(InvoiceAdviceService $invoiceAdviceService)
    {
        $this->invoiceAdviceService = $invoiceAdviceService;
    }

    /**
     * @OA\Get(
     *     path="/api/invoice-advice",
     *     tags={"Invoice Advice"},
     *     summary="Get a list of invoice advice records with filters and pagination",
     *     description="Fetches a list of invoice advice records, with optional filtering based on fields in the invoice advice table, and supports pagination.",
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
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter by end date",
     *         @OA\Schema(type="string", format="date")
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/invoice-advice?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/InvoiceAdvice")
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

            $invoiceAdvice = $this->invoiceAdviceService->getAllWithFilters($filters, $per_page);

            return InvoiceAdviceResource::collection($invoiceAdvice)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $invoiceAdvice->currentPage(),
                        'next_page_url' => $invoiceAdvice->nextPageUrl(),
                        'prev_page_url' => $invoiceAdvice->previousPageUrl(),
                        'per_page' => $invoiceAdvice->perPage(),
                        'total' => $invoiceAdvice->total(),
                        'last_page' => $invoiceAdvice->lastPage(),
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

    public function store(Request $request)
    {
        try {

            if (!isset($request->customer_id) || !isset($request->customer_site_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer ID and Customer Site ID are required.',
                ], 400);
            }

            //TODO verify using zone_id to get Zone Name, or using site_address
            $customerSite = CustomerSite::find($request->customer_site_id);
            if (!$customerSite) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer Site not found.',
                ], 404);
            }

            // Get the start and end dates of the previous month
            $startOfLastMonth = now()->subMonth()->startOfMonth();
            $endOfLastMonth = now()->subMonth()->endOfMonth();

            // Check if there's a record created in the last month
            $recordExists = InvoiceAdvice::whereBetween('date', [$startOfLastMonth, $endOfLastMonth])->exists();

            if ($recordExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Record already exists for the last month.',
                ]);
            }

            $request->merge([
                'date' => now()->subMonth(),
                'department' => "Gas Distribution {$customerSite->site_address}",

                // TODO confirm these fields
                'gcc_created_by' => auth()->user()->id,
                'invoice_advice_created_by' => auth()->user()->id,
            ]);

            $invoiceItem = $this->invoiceAdviceService->create($request->all());

            return (new InvoiceAdviceResource($invoiceItem))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get details of a specific InvoiceAdvice record by ID.
     *
     * @OA\Get(
     *     path="/api/invoice-advice/{id}",
     *     tags={"Invoice Advice"},
     *     summary="Get details of a specific InvoiceAdvice record",
     *     description="Fetches details of a specific InvoiceAdvice record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdvice record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/InvoiceAdvice")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the InvoiceAdvice record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $invoiceAdvice = $this->invoiceAdviceService->getById($id);

            return (new InvoiceAdviceResource($invoiceAdvice))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Advice record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific InvoiceAdvice record by ID.
     *
     * @OA\Delete(
     *     path="/api/invoice-advice/{id}",
     *     tags={"Invoice Advice"},
     *     summary="Delete a specific InvoiceAdvice record",
     *     description="Deletes a specific InvoiceAdvice record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdvice record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice Advice record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the InvoiceAdvice record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $invoiceAdvice = $this->invoiceAdviceService->getById($id);
            $invoiceAdvice->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice Advice record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Advice record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

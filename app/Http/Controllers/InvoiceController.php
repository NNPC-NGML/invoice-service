<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * The InvoiceService instance.
     *
     * @var InvoiceService
     */
    protected InvoiceService $invoiceService;

    /**
     * InvoiceController constructor.
     *
     * @param InvoiceService $invoiceService
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * @OA\Get(
     *     path="/api/invoices",
     *     tags={"Invoices"},
     *     summary="Get a list of invoices with filters and pagination",
     *     description="Fetches a list of invoices, with optional filtering based on fields in the invoices table, and supports pagination.",
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
     *         name="status",
     *         in="query",
     *         description="Filter by invoice status",
     *         @OA\Schema(type="integer")
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/invoices?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Invoice")
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

            $invoices = $this->invoiceService->getAllWithFilters($filters, $per_page);

            return InvoiceResource::collection($invoices)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $invoices->currentPage(),
                        'next_page_url' => $invoices->nextPageUrl(),
                        'prev_page_url' => $invoices->previousPageUrl(),
                        'per_page' => $invoices->perPage(),
                        'total' => $invoices->total(),
                        'last_page' => $invoices->lastPage(),
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
     * Get details of a specific Invoice record by ID.
     *
     * @OA\Get(
     *     path="/api/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Get details of a specific Invoice record",
     *     description="Fetches details of a specific Invoice record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Invoice record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/Invoice")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the Invoice record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $invoice = $this->invoiceService->getById($id);

            return (new InvoiceResource($invoice))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific Invoice record by ID.
     *
     * @OA\Delete(
     *     path="/api/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Delete a specific Invoice record",
     *     description="Deletes a specific Invoice record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Invoice record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the Invoice record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $invoice = $this->invoiceService->getById($id);
            $invoice->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

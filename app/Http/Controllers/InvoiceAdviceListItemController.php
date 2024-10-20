<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceAdviceListItemResource;
use App\Services\InvoiceAdviceListItemService;
use Illuminate\Http\Request;

class InvoiceAdviceListItemController extends Controller
{
    /**
     * The InvoiceAdviceListItemService instance.
     *
     * @var InvoiceAdviceListItemService
     */
    protected InvoiceAdviceListItemService $invoiceAdviceListItemService;

    /**
     * InvoiceAdviceListItemController constructor.
     *
     * @param InvoiceAdviceListItemService $invoiceAdviceListItemService
     */
    public function __construct(InvoiceAdviceListItemService $invoiceAdviceListItemService)
    {
        $this->invoiceAdviceListItemService = $invoiceAdviceListItemService;
    }

    /**
     * @OA\Get(
     *     path="/api/invoice-advice-list-items",
     *     tags={"Invoice Advice List Items"},
     *     summary="Get a list of invoice advice list items with filters and pagination",
     *     description="Fetches a list of invoice advice list items, with optional filtering and pagination support.",
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/invoice-advice-list-items?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/InvoiceAdviceListItem")
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

            $invoiceItems = $this->invoiceAdviceListItemService->getAllWithFilters($filters, $per_page);

            return InvoiceAdviceListItemResource::collection($invoiceItems)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $invoiceItems->currentPage(),
                        'next_page_url' => $invoiceItems->nextPageUrl(),
                        'prev_page_url' => $invoiceItems->previousPageUrl(),
                        'per_page' => $invoiceItems->perPage(),
                        'total' => $invoiceItems->total(),
                        'last_page' => $invoiceItems->lastPage(),
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
     * Get details of a specific InvoiceAdviceListItem by ID.
     *
     * @OA\Get(
     *     path="/api/invoice-advice-list-items/{id}",
     *     tags={"Invoice Advice List Items"},
     *     summary="Get details of a specific InvoiceAdviceListItem",
     *     description="Fetches details of a specific InvoiceAdviceListItem by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdviceListItem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/InvoiceAdviceListItem")
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
            $invoiceItem = $this->invoiceAdviceListItemService->getById($id);

            return (new InvoiceAdviceListItemResource($invoiceItem))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice advice list item not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific InvoiceAdviceListItem by ID.
     *
     * @OA\Delete(
     *     path="/api/invoice-advice-list-items/{id}",
     *     tags={"Invoice Advice List Items"},
     *     summary="Delete a specific InvoiceAdviceListItem",
     *     description="Deletes a specific InvoiceAdviceListItem by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the InvoiceAdviceListItem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice advice list item deleted successfully"
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
            $invoiceItem = $this->invoiceAdviceListItemService->getById($id);
            $invoiceItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice advice list item deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice advice list item not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

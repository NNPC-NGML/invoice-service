<?php

namespace App\Http\Controllers;

use App\Http\Resources\GccApprovedByCustomerResource;
use App\Services\GccApprovedByCustomerService;
use Illuminate\Http\Request;

class GccApprovedByCustomerController extends Controller
{
    /**
     * The GccApprovedByCustomerService instance.
     *
     * @var GccApprovedByCustomerService
     */
    protected GccApprovedByCustomerService $gccApprovedByCustomerService;

    /**
     * GccApprovedByCustomerController constructor.
     *
     * @param GccApprovedByCustomerService $gccApprovedByCustomerService
     */
    public function __construct(GccApprovedByCustomerService $gccApprovedByCustomerService)
    {
        $this->gccApprovedByCustomerService = $gccApprovedByCustomerService;
    }

    /**
     * @OA\Get(
     *     path="/api/gcc-approved-by-customers",
     *     tags={"GCC Approved By Customers"},
     *     summary="Get a list of customer approvals with filters and pagination",
     *     description="Fetches a list of customer approvals, with optional filtering based on fields in the gcc_approved_by_customers table, and supports pagination.",
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/gcc-approved-by-customers?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GccApprovedByCustomer")
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

            $customerApprovals = $this->gccApprovedByCustomerService->getAllWithFilters($filters, $per_page);

            return GccApprovedByCustomerResource::collection($customerApprovals)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $customerApprovals->currentPage(),
                        'next_page_url' => $customerApprovals->nextPageUrl(),
                        'prev_page_url' => $customerApprovals->previousPageUrl(),
                        'per_page' => $customerApprovals->perPage(),
                        'total' => $customerApprovals->total(),
                        'last_page' => $customerApprovals->lastPage(),
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
     * @OA\Post(
     *     path="/api/gcc-approved-by-customers",
     *     tags={"Gcc Approved By Customer"},
     *     summary="Store a new GCC approved record",
     *     description="Creates a new GCC approved record with the provided customer information, signature, and date.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_name", "signature", "date"},
     *             @OA\Property(property="customer_name", type="string", maxLength=255, example="John Doe", description="Name of the customer"),
     *             @OA\Property(property="signature", type="string", example="Base64EncodedSignature", description="Customer's digital signature in base64 format"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-11-13", description="Date of the approval")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Record created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/GccApprovedByCustomer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
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
    public function store(Request $request)
    {
        try {
            $gccApprovedRecord = $this->gccApprovedByCustomerService->create($request->all());

            return (new GccApprovedByCustomerResource($gccApprovedRecord))
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
     * Get details of a specific GccApprovedByCustomer record by ID.
     *
     * @OA\Get(
     *     path="/api/gcc-approved-by-customers/{id}",
     *     tags={"GCC Approved By Customers"},
     *     summary="Get details of a specific customer approval record",
     *     description="Fetches details of a specific customer approval record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the customer approval record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/GccApprovedByCustomer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id The ID of the customer approval record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $customerApproval = $this->gccApprovedByCustomerService->getById($id);

            return (new GccApprovedByCustomerResource($customerApproval))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer approval record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific customer approval record by ID.
     *
     * @OA\Delete(
     *     path="/api/gcc-approved-by-customers/{id}",
     *     tags={"GCC Approved By Customers"},
     *     summary="Delete a specific customer approval record",
     *     description="Deletes a specific customer approval record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the customer approval record",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Customer approval record deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param int $id The ID of the customer approval record.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $customerApproval = $this->gccApprovedByCustomerService->getById($id);
            $customerApproval->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer approval record deleted successfully'
            ], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer approval record not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}

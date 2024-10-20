<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterTemplateResource;
use App\Services\LetterTemplateService;
use Illuminate\Http\Request;

class LetterTemplateController extends Controller
{
    /**
     * The LetterTemplateService instance.
     *
     * @var LetterTemplateService
     */
    protected LetterTemplateService $letterTemplateService;

    /**
     * LetterTemplateController constructor.
     *
     * @param LetterTemplateService $letterTemplateService
     */
    public function __construct(LetterTemplateService $letterTemplateService)
    {
        $this->letterTemplateService = $letterTemplateService;
    }

    /**
     * @OA\Get(
     *     path="/api/letter-templates",
     *     tags={"Letter Templates"},
     *     summary="Get a list of letter templates with filters and pagination",
     *     description="Fetches a list of letter templates, with optional filtering and pagination support.",
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
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/letter-templates?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=200),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/LetterTemplate")
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

            $letterTemplates = $this->letterTemplateService->getAll($filters, $per_page);

            return LetterTemplateResource::collection($letterTemplates)
                ->additional([
                    'status' => 'success',
                    'pagination' => [
                        'current_page' => $letterTemplates->currentPage(),
                        'next_page_url' => $letterTemplates->nextPageUrl(),
                        'prev_page_url' => $letterTemplates->previousPageUrl(),
                        'per_page' => $letterTemplates->perPage(),
                        'total' => $letterTemplates->total(),
                        'last_page' => $letterTemplates->lastPage(),
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
     * Get details of a specific LetterTemplate by ID.
     *
     * @OA\Get(
     *     path="/api/letter-templates/{id}",
     *     tags={"Letter Templates"},
     *     summary="Get details of a specific LetterTemplate",
     *     description="Fetches details of a specific LetterTemplate by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the LetterTemplate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/LetterTemplate")
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
            $letterTemplate = $this->letterTemplateService->getById($id);

            return (new LetterTemplateResource($letterTemplate))
                ->additional(['status' => 'success'])
                ->response();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Letter template not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new LetterTemplate.
     *
     * @OA\Post(
     *     path="/api/letter-templates",
     *     tags={"Letter Templates"},
     *     summary="Create a new LetterTemplate",
     *     description="Creates a new LetterTemplate.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="letter", type="string", example="Your letter content here"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="LetterTemplate created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(ref="#/components/schemas/LetterTemplate")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'letter' => 'required|string',
            'status' => 'required|integer',
        ]);

        try {
            $letterTemplate = $this->letterTemplateService->create($request->all());

            return (new LetterTemplateResource($letterTemplate))
                ->additional(['status' => 'success'])
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a specific LetterTemplate by ID.
     *
     * @OA\Delete(
     *     path="/api/letter-templates/{id}",
     *     tags={"Letter Templates"},
     *     summary="Delete a specific LetterTemplate",
     *     description="Deletes a specific LetterTemplate by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the LetterTemplate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="LetterTemplate deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $letterTemplate = $this->letterTemplateService->getById($id);
            $letterTemplate->delete();

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Letter template not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

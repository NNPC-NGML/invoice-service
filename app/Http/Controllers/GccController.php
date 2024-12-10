<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Gcc;
use App\Models\DailyVolume;
use App\Services\GccService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\GccResource;
use App\Services\InvoiceAdviceListItemService;

class GccController extends Controller
{

    protected $gccService;
    /**
     * The InvoiceAdviceListItemService instance.
     *
     * @var InvoiceAdviceListItemService
     */
    protected InvoiceAdviceListItemService $invoiceAdviceListItemService;


    /**
     * GccApprovedByCustomerController constructor.
     *
     * @param GccApprovedByCustomerService $gccApprovedByCustomerService
     */
    public function __construct(GccService $gccService, InvoiceAdviceListItemService $invoiceAdviceListItemService)
    {
        $this->gccService = $gccService;
        $this->invoiceAdviceListItemService = $invoiceAdviceListItemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = auth();
        DB::beginTransaction();
        //'capex_recovery_amount' => 0, would come based on customer check
        // 'with_vat' => 0, would come based on customer check
        //  'department_id' => 1, would come based on users department relationship
        //  create constants properties for status as they are 11
        try {
            $data = [
                "customer_id" => $request->customer_id,
                "customer_site_id" => $request->customer_site_id,
                'gcc_date' => Carbon::now()->subMonth()->subDay(),
                'capex_recovery_amount' => 0,
                'with_vat' => 0,
                'gcc_created_by' => $userId->id(),
                'letter_id' => 1,
                'department_id' => 1,
                'status' => 11,
            ];
            $gcc = $this->gccService->create($data);
            if ($gcc) {

                // decrypt the list items
                $decryptedListItems = json_decode($request->list_item);
                // restructure data 
                $listItemsData = [];

                foreach ($decryptedListItems as $item) {
                    $listItemsData[] = [
                        "gcc_id" => $gcc->id,
                        'customer_id' => $request->customer_id,
                        'customer_site_id' => $request->customer_site_id,
                        'daily_volume_id' => $item->id,
                        'volume' => $item->volume,
                        'inlet' => $item->inlet_pressure,
                        'outlet' => $item->outlet_pressure,
                        'allocation' => $item->allocation,
                        'nomination' => $item->nomination,
                        'original_date' => $item->created_at,
                        'status' => 1,
                        "created_by" => $item->created_by,
                        "approved_by" => $item->approved_by,
                    ];
                }
                $createListItem = $this->invoiceAdviceListItemService->bulkInsert($listItemsData);

                if ($createListItem) {
                    DB::commit();
                    $responseData = new GccResource($gcc);
                    return $responseData->additional([
                        "status" => "success",
                        "message" => "Gcc created successfully",
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Gcc $gcc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gcc $gcc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGccRequest $request, Gcc $gcc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gcc $gcc)
    {
        //
    }

    public function initiateGcc(Request $request)
    {
        // validate the request
        // go to the gcc table and confirm for recods 

        $getLastGcc = $this->gccService->getLastGccByCustomerAndCustomerSite($request->customer_id, $request->customer_site_id);

        // if the last gcc record exists, then check if the current gcc created_at month is equal to current month - 1

        if ($getLastGcc) {

            if ((Carbon::parse($getLastGcc->gcc_date))->month == Carbon::now()->subMonth()->month) {

                $listItems = $this->invoiceAdviceListItemService->getAllWithFilters(["gcc_id"  => 1], 0);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        "list_item" => $listItems,
                        "gcc" => $getLastGcc,
                        "invoice_advice" => null,
                        "invoice" => null,
                    ],
                ], 200);
            }
        }

        // this means no valid gcc record exists for the customer and customer site for that month
        // get list items from daily volume table
        // return gcc as null
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();
        $listItems = DailyVolume::where('customer_id', $request->customer_id)
            ->where('customer_site_id', $request->customer_site_id)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                "list_item" => $listItems,
                "gcc" => null,
                "invoice_advice" => null,
                "invoice" => null,
            ],
        ], 200);
    }
}

<?php

namespace App\Services;

use App\Models\DailyVolume;
use App\Models\InvoiceAdvice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceAdviceService
{
    /**
     * Validate the provided Invoice Advice data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateInvoiceAdvice(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'with_vat' => 'nullable|boolean',
            'customer_id' => 'required|integer',
            'customer_site_id' => 'required|integer',
            'capex_recovery_amount' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'sometimes|integer',
            'department' => 'required|string',
            'gcc_created_by_id' => 'nullable|integer',
            'invoice_advice_created_by_id' => 'nullable|integer',
        ] : [
            'with_vat' => 'sometimes|required|boolean',
            'customer_id' => 'sometimes|required|integer',
            'customer_site_id' => 'sometimes|required|integer',
            'capex_recovery_amount' => 'sometimes|required|string',
            'date' => 'sometimes|date',
            'status' => 'sometimes|required|integer',
            'department' => 'sometimes|required|string',
            'gcc_created_by_id' => 'sometimes|nullable|integer',
            'invoice_advice_created_by_id' => 'sometimes|nullable|integer',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }


    /**
     * Get all invoice advice records with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of invoice advice records.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = InvoiceAdvice::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if ($key !== 'with_vat' && $key !== 'customer_id' && $key !== 'customer_site_id' && $key !== 'status' && $key !== 'date_from' && $key !== 'date_to') {
                continue;
            }
            switch ($key) {
                case 'date_from':
                    $query->whereDate('date', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('date', '<=', $value);
                    break;
                default:
                    $query->where($key, $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get all invoice advice records.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return InvoiceAdvice::paginate($per_page);
    }

    /**
     * Find an invoice advice entry by its ID.
     *
     * @param int $id The ID of the invoice advice entry.
     * @return InvoiceAdvice The found invoice advice entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): InvoiceAdvice
    {
        return InvoiceAdvice::findOrFail($id);
    }

    /**
     * Create a new invoice advice entry.
     *
     * @param array $data The data for creating the invoice advice.
     * @return InvoiceAdvice The newly created invoice advice entry.
     * @throws \Throwable
     */
    public function create(array $data): InvoiceAdvice
    {
        Log::info('Starting invoice advice creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                $startOfLastMonth = now()->subMonth()->startOfMonth();
                $endOfLastMonth = now()->subMonth()->endOfMonth();
                $dailyVolumes = DailyVolume::where('customer_id', $data['customer_id'])
                    ->where('customer_site_id', $data['customer_site_id'])
                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->orderBy('created_at')
                    ->get();



                $validatedData = $this->validateInvoiceAdvice($data);
                $invoiceAdviceCreated = InvoiceAdvice::create($validatedData);

                $inoiceAdviceListItemService = new InvoiceAdviceListItemService();
                $items = [];
                $total_quantity_of_gas = 0;
                foreach ($dailyVolumes as $dailyVolume) {
                    $total_quantity_of_gas += $dailyVolume->volume;
                    $item = [
                        'customer_id' => $data['customer_id'],
                        'customer_site_id' => $data['customer_site_id'],
                        'invoice_advice_id' => $invoiceAdviceCreated->id,
                        'daily_volume_id' => $dailyVolume->id,
                        'volume' => $dailyVolume->volume,
                        'date' => $invoiceAdviceCreated->date,
                    ];
                    $items[] = $item;
                }

                $insert = $inoiceAdviceListItemService->bulkInsert($items);
                if ($insert) {
                    if($dailyVolumes->count() > 0) {
                        $invoiceAdviceCreated->from_date = $dailyVolumes->first()->created_at;
                        $invoiceAdviceCreated->to_date = $dailyVolumes->last()->created_at;
                    }
                    $invoiceAdviceCreated->total_quantity_of_gas = $total_quantity_of_gas;
                    $invoiceAdviceCreated->save();
                    $invoiceAdviceCreated->load('invoice_advice_list_items');
                    $invoiceAdviceCreated->load('customer');
                    $invoiceAdviceCreated->load('customer_site');
                    $invoiceAdviceCreated->load('gcc_created_by');
                    $invoiceAdviceCreated->load('invoice_advice_created_by');
                    return $invoiceAdviceCreated;
                }

                throw new \Exception('Failed to create invoice advice');
            } catch (\Throwable $e) {
                Log::error('Unexpected error during invoice advice creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing invoice advice record.
     *
     * @param array $data The data to update the record with.
     * @return InvoiceAdvice The updated invoice advice record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): InvoiceAdvice
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting invoice advice update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Invoice Advice record by its ID
            $invoiceAdvice = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateInvoiceAdvice($data, true);

            // Perform the update on the existing Invoice Advice record
            $invoiceAdvice->update($validatedData);

            Log::info('Invoice advice updated successfully', ['id' => $invoiceAdvice->id]);

            return $invoiceAdvice;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during invoice advice update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

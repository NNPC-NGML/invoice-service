<?php

namespace App\Services;

use App\Models\InvoiceAdviceListItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceAdviceListItemService
{
    /**
     * Validate the provided Invoice Advice List Item data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateInvoiceAdviceListItem(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'customer_id' => 'required|integer',
            'customer_site_id' => 'required|integer',
            'invoice_advice_id' => 'required|integer',
            'daily_volume_id' => 'required|integer',
            'volume' => 'required|string',
            'inlet' => 'nullable|string',
            'outlet' => 'nullable|string',
            'take_or_pay_value' => 'nullable|string',
            'allocation' => 'nullable|string',
            'daily_target' => 'nullable|string',
            'nomination' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'sometimes|integer',
        ] : [
            'customer_id' => 'sometimes|required|integer',
            'customer_site_id' => 'sometimes|required|integer',
            'invoice_advice_id' => 'sometimes|required|integer',
            'daily_volume_id' => 'sometimes|required|integer',
            'volume' => 'sometimes|required|string',
            'inlet' => 'nullable|string',
            'outlet' => 'nullable|string',
            'take_or_pay_value' => 'nullable|string',
            'allocation' => 'nullable|string',
            'daily_target' => 'nullable|string',
            'nomination' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'status' => 'sometimes|integer',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }


    /**
     * Get all invoice advice list items with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of invoice advice list items.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = InvoiceAdviceListItem::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if ($key !== 'customer_id' && $key !== 'customer_site_id' && $key !== 'volume' && $key !== 'inlet' && $key !== 'outlet' && $key !== 'take_or_pay_value' && $key !== 'allocation' && $key !== 'daily_target' && $key !== 'nomination' && $key !== 'daily_gas_id' && $key !== 'date' && $key !== 'status') {
                continue;
            }
            $query->where($key, $value);
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated invoice advice list items.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return InvoiceAdviceListItem::paginate($per_page);
    }

    /**
     * Find an invoice advice list item by its ID.
     *
     * @param int $id The ID of the invoice advice list item.
     * @return InvoiceAdviceListItem The found invoice advice list item.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no item is found.
     */
    public function getById(int $id): InvoiceAdviceListItem
    {
        return InvoiceAdviceListItem::findOrFail($id);
    }

    /**
     * Create a new invoice advice list item.
     *
     * @param array $data The data for creating the invoice advice list item.
     * @return InvoiceAdviceListItem The newly created invoice advice list item.
     * @throws \Throwable
     */
    public function create(array $data): InvoiceAdviceListItem
    {
        Log::info('Starting invoice advice list item creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Invoice Advice List Item
                $validatedData = $this->validateInvoiceAdviceListItem($data);
                $invoiceAdviceListItemCreated = InvoiceAdviceListItem::create($validatedData);

                return $invoiceAdviceListItemCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during invoice advice list item creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Create a new invoice advice list item.
     *
     * @param array $data The data for creating the invoice advice list item.
     * @return bool The status of the bulk insertion.
     * @throws \Throwable
     */
    public function bulkInsert(array $data): bool
    {
        Log::info('Starting invoice advice list item creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Invoice Advice List Item
                $insertionData = [];
                foreach ($data as $item) {
                    $insertionData[] = $this->validateInvoiceAdviceListItem($item);
                }

                return InvoiceAdviceListItem::insert($insertionData);
            } catch (\Throwable $e) {
                Log::error('Unexpected error during invoice advice list item creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing invoice advice list item.
     *
     * @param array $data The data to update the record with.
     * @return InvoiceAdviceListItem The updated invoice advice list item.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): InvoiceAdviceListItem
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting invoice advice list item update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Invoice Advice List Item record by its ID
            $invoiceAdviceListItem = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateInvoiceAdviceListItem($data, true);

            // Perform the update on the existing Invoice Advice List Item record
            $invoiceAdviceListItem->update($validatedData);

            Log::info('Invoice advice list item updated successfully', ['id' => $invoiceAdviceListItem->id]);

            return $invoiceAdviceListItem;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during invoice advice list item update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

<?php

namespace App\Services;

use App\Models\InvoiceAdviceApprovedBy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceAdviceApprovedByService
{
    /**
     * Validate the provided Invoice Advice Approved Bie data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateInvoiceAdviceApprovedBy(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'user_id' => 'required|integer',
            'invoice_advice_id' => 'required|integer',
            'approval_for' => 'required|integer',
            'date' => 'required|date',
        ] : [
            'user_id' => 'sometimes|required|integer',
            'invoice_advice_id' => 'sometimes|required|integer',
            'approval_for' => 'sometimes|required|integer',
            'date' => 'sometimes|required|date',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all invoice advice approved bies with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of invoice advice approved bies.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = InvoiceAdviceApprovedBy::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'user_id':
                case 'invoice_advice_id':
                case 'approval_for':
                    $query->where($key, $value);
                    break;
                case 'date_from':
                    $query->whereDate('date', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('date', '<=', $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated invoice advice approved bies.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return InvoiceAdviceApprovedBy::paginate($per_page);
    }

    /**
     * Find an invoice advice approved bie by its ID.
     *
     * @param int $id The ID of the invoice advice approved bie.
     * @return InvoiceAdviceApprovedBy The found invoice advice approved bie.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): InvoiceAdviceApprovedBy
    {
        return InvoiceAdviceApprovedBy::findOrFail($id);
    }

    /**
     * Create a new invoice advice approved bie entry.
     *
     * @param array $data The data for creating the invoice advice approved bie.
     * @return InvoiceAdviceApprovedBy The newly created invoice advice approved bie entry.
     * @throws \Throwable
     */
    public function create(array $data): InvoiceAdviceApprovedBy
    {
        Log::info('Starting invoice advice approved bie creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Invoice Advice Approved Bie entry
                $validatedData = $this->validateInvoiceAdviceApprovedBy($data);
                $invoiceAdviceApprovedByCreated = InvoiceAdviceApprovedBy::create($validatedData);

                return $invoiceAdviceApprovedByCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during invoice advice approved bie creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing invoice advice approved bie record.
     *
     * @param array $data The data to update the record with.
     * @return InvoiceAdviceApprovedBy The updated invoice advice approved bie record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): InvoiceAdviceApprovedBy
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting invoice advice approved bie update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Invoice Advice Approved Bie record by its ID
            $invoiceAdviceApprovedBy = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateInvoiceAdviceApprovedBy($data, true);

            // Perform the update on the existing Invoice Advice Approved Bie record
            $invoiceAdviceApprovedBy->update($validatedData);

            Log::info('Invoice advice approved bie updated successfully', ['id' => $invoiceAdviceApprovedBy->id]);

            return $invoiceAdviceApprovedBy;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during invoice advice approved bie update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    /**
     * Validate the provided Invoice data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateInvoice(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'invoice_number' => 'required|string|max:255',
            'invoice_advice_id' => 'required|integer',
            'consumed_volume_amount_in_naira' => 'required|numeric|min:0',
            'consumed_volume_amount_in_dollar' => 'required|numeric|min:0',
            'dollar_to_naira_convertion_rate' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total_volume_paid_for' => 'required|numeric|min:0',
            'status' => 'required|integer',
        ] : [
            'invoice_number' => 'sometimes|required|string|max:255',
            'invoice_advice_id' => 'sometimes|required|integer',
            'consumed_volume_amount_in_naira' => 'sometimes|required|numeric|min:0',
            'consumed_volume_amount_in_dollar' => 'sometimes|required|numeric|min:0',
            'dollar_to_naira_convertion_rate' => 'sometimes|required|numeric|min:0',
            'vat_amount' => 'sometimes|required|numeric|min:0',
            'total_volume_paid_for' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|integer',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all invoices with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of invoices.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = Invoice::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'status':
                    $query->where('status', $value);
                    break;
                // Add more filters here if needed
                default:
                    // Ignore unknown filters
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated invoices.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return Invoice::paginate($per_page);
    }

    /**
     * Find an invoice by its ID.
     *
     * @param int $id The ID of the invoice.
     * @return Invoice The found invoice.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): Invoice
    {
        return Invoice::findOrFail($id);
    }

    /**
     * Create a new invoice.
     *
     * @param array $data The data for creating the invoice.
     * @return Invoice The newly created invoice.
     * @throws \Throwable
     */
    public function create(array $data): Invoice
    {
        Log::info('Starting invoice creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Invoice entry
                $validatedData = $this->validateInvoice($data);
                $invoiceCreated = Invoice::create($validatedData);

                return $invoiceCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during invoice creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing invoice record.
     *
     * @param array $data The data to update the record with.
     * @return Invoice The updated invoice record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): Invoice
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting invoice update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Invoice record by its ID
            $invoice = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateInvoice($data, true);

            // Perform the update on the existing Invoice record
            $invoice->update($validatedData);

            Log::info('Invoice updated successfully', ['id' => $invoice->id]);

            return $invoice;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during invoice update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

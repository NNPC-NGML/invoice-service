<?php

namespace App\Services;

use App\Models\GccApprovedByCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GccApprovedByCustomerService
{
    /**
     * Validate the provided Customer Approval data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateCustomerApproval(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'customer_name' => 'required|string|max:255',
            'signature' => 'required|string',
            'date' => 'required|date',
        ] : [
            'customer_name' => 'sometimes|required|string|max:255',
            'signature' => 'sometimes|required|string',
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
     * Get all customer approvals with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply. Supported keys:
     *                       - 'customer_name': Filter records by customer name.
     *                       - 'date': Filter records by approval date.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of customer approvals.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = GccApprovedByCustomer::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if (!in_array($key, ['customer_name', 'date'])) {
                continue;
            }
            switch ($key) {
                case 'date':
                    $query->whereDate('date', $value);
                    break;
                default:
                    // Apply other filters directly (e.g., customer_name)
                    $query->where($key, 'like', '%' . $value . '%');
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated customer approvals.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return GccApprovedByCustomer::paginate($per_page);
    }

    /**
     * Find a customer approval entry by its ID.
     *
     * @param int $id The ID of the customer approval entry.
     * @return GccApprovedByCustomer The found customer approval entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): GccApprovedByCustomer
    {
        return GccApprovedByCustomer::findOrFail($id);
    }

    /**
     * Create a new customer approval entry.
     *
     * @param array $data The data for creating the customer approval.
     * @return GccApprovedByCustomer The newly created customer approval entry.
     * @throws \Throwable
     */
    public function create(array $data): GccApprovedByCustomer
    {
        Log::info('Starting customer approval creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Customer Approval entry
                $validatedData = $this->validateCustomerApproval($data);
                $customerApprovalCreated = GccApprovedByCustomer::create($validatedData);

                return $customerApprovalCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during customer approval creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing customer approval record.
     *
     * @param array $data The data to update the record with.
     * @return GccApprovedByCustomer The updated customer approval record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): GccApprovedByCustomer
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting customer approval update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Customer Approval record by its ID
            $customerApproval = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateCustomerApproval($data, true);

            // Perform the update on the existing Customer Approval record
            $customerApproval->update($validatedData);

            Log::info('Customer approval updated successfully', ['id' => $customerApproval->id]);

            return $customerApproval;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during customer approval update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

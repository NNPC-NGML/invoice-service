<?php

namespace App\Services;

use App\Models\GccApprovedByAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GccApprovedByAdminService
{
    /**
     * Validate the provided approval data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateApproval(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'user_id' => 'required|integer', // Ensure user exists
            'invoice_advice_id' => 'required|integer', // Ensure invoice advice exists
            'date' => 'required|date',
        ] : [
            'user_id' => 'sometimes|required|integer',
            'invoice_advice_id' => 'sometimes|required|integer',
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
     * Get all approvals with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply. Supported keys:
     *                       - 'user_id': Filter by user ID.
     *                       - 'invoice_advice_id': Filter by invoice advice ID.
     *                       - 'date': Filter by date.
     *                       - Additional keys for filtering other columns.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of approvals.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = GccApprovedByAdmin::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if (!in_array($key, ['user_id', 'invoice_advice_id', 'date'])) {
                continue;
            }
            switch ($key) {
                case 'date':
                    $query->whereDate('date', $value);
                    break;
                default:
                    // Apply other filters directly
                    $query->where($key, $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated approvals.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return GccApprovedByAdmin::paginate($per_page);
    }

    /**
     * Find an approval entry by its ID.
     *
     * @param int $id The ID of the approval entry.
     * @return GccApprovedByAdmin The found approval entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): GccApprovedByAdmin
    {
        return GccApprovedByAdmin::findOrFail($id);
    }

    /**
     * Create a new approval entry.
     *
     * @param array $data The data for creating the approval.
     * @return GccApprovedByAdmin The newly created approval entry.
     * @throws \Throwable
     */
    public function create(array $data): GccApprovedByAdmin
    {
        Log::info('Starting approval creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                $check = GccApprovedByAdmin::where('invoice_advice_id', $data['invoice_advice_id']);
                if ($check->exists()) {
                    throw new \Exception('Invoice advice already approved by admin');
                }

                // Validate and create the approval entry
                $validatedData = $this->validateApproval($data);
                $approvalCreated = GccApprovedByAdmin::create($validatedData);

                if($approvalCreated) {
                    // update gcc_created_by
                    $service = new InvoiceAdviceService();
                    $service->update(['gcc_created_by' => $approvalCreated->user_id, 'id' => $approvalCreated->invoice_advice_id]);
                }

                return $approvalCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during approval creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing approval record.
     *
     * @param array $data The data to update the record with.
     * @return GccApprovedByAdmin The updated approval record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): GccApprovedByAdmin
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting approval update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing approval record by its ID
            $approval = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateApproval($data, true);

            // Perform the update on the existing approval record
            $approval->update($validatedData);

            Log::info('Approval updated successfully', ['id' => $approval->id]);

            return $approval;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during approval update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

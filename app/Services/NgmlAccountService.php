<?php

namespace App\Services;

use App\Models\NgmlAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NgmlAccountService
{
    /**
     * Validate the provided Ngml Account data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateNgmlAccount(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'bank_name' => 'required|string|max:255',
            'bank_address' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'sort_code' => 'required|string|max:255',
            'tin' => 'required|string|max:255',
        ] : [
            'bank_name' => 'sometimes|required|string|max:255',
            'bank_address' => 'sometimes|required|string|max:255',
            'account_name' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|string|max:255',
            'sort_code' => 'sometimes|required|string|max:255',
            'tin' => 'sometimes|required|string|max:255',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all ngml accounts with optional filters and pagination.
     *
     * @param array $filters An associative array of filters to apply.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of ngml accounts.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = NgmlAccount::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if (!in_array($key, ['bank_name', 'bank_address', 'account_name', 'account_number', 'sort_code', 'tin'])) {
                continue;
            }
            $query->where($key, 'like', "%$value%");
        }

        // Paginate the results
        return $query->paginate($per_page);
    }

    /**
     * Get paginated ngml accounts.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return NgmlAccount::paginate($per_page);
    }

    /**
     * Find an ngml account entry by its ID.
     *
     * @param int $id The ID of the ngml account entry.
     * @return NgmlAccount The found ngml account entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): NgmlAccount
    {
        return NgmlAccount::findOrFail($id);
    }

    /**
     * Create a new ngml account entry.
     *
     * @param array $data The data for creating the ngml account.
     * @return NgmlAccount The newly created ngml account entry.
     * @throws \Throwable
     */
    public function create(array $data): NgmlAccount
    {
        Log::info('Starting ngml account creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the Ngml Account entry
                $validatedData = $this->validateNgmlAccount($data);
                $ngmlAccountCreated = NgmlAccount::create($validatedData);

                return $ngmlAccountCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during ngml account creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing ngml account record.
     *
     * @param array $data The data to update the record with.
     * @return NgmlAccount The updated ngml account record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): NgmlAccount
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting ngml account update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Ngml Account record by its ID
            $ngmlAccount = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateNgmlAccount($data, true);

            // Perform the update on the existing Ngml Account record
            $ngmlAccount->update($validatedData);

            Log::info('Ngml account updated successfully', ['id' => $ngmlAccount->id]);

            return $ngmlAccount;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during ngml account update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

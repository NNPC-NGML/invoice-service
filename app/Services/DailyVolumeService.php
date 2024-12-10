<?php

namespace App\Services;

use App\Http\Resources\DailyVolumeResource;
use App\Jobs\GasConsumption\GasConsumptionCreated;
use App\Jobs\GasConsumption\GasConsumptionUpdated;
use App\Models\DailyVolume;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class DailyVolumeService
{
    /**
     * Validate the provided Daily Volume data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function validateDailyVolume(array $data, bool $is_update = false): array
    {
        // Define validation rules based on whether it's an update or create request
        $rules = $is_update === false ? [
            'customer_id' => 'required|integer',
            'customer_site_id' => 'required|integer',
            'volume' => 'required|numeric|min:0',
            // 'rate' => 'required|numeric|min:0',
            // 'amount' => 'required|numeric|min:0',
        ] : [
            'customer_id' => 'sometimes|required|integer',
            'customer_site_id' => 'sometimes|required|integer',
            'volume' => 'sometimes|required|numeric|min:0',
            // 'rate' => 'sometimes|required|numeric|min:0',
            // 'amount' => 'sometimes|required|numeric|min:0',
        ];

        // Run the validator with the specified rules
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get all daily volumes with optional filters and pagination.
     *
     * This method allows filtering daily volume records based on the provided filters.
     * It supports date range filters for `created_at` and `updated_at`, as well as
     * other column-based filters. The result is paginated.
     *
     * @param array $filters An associative array of filters to apply. Supported keys:
     *                       - 'created_at_from': Filter records where `created_at` is after or on this date.
     *                       - 'created_at_to': Filter records where `created_at` is before or on this date.
     *                       - 'updated_at_from': Filter records where `updated_at` is after or on this date.
     *                       - 'updated_at_to': Filter records where `updated_at` is before or on this date.
     *                       - Additional keys for filtering other columns.
     * @param int $per_page The number of records to return per page. Defaults to 50.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginated list of daily volumes.
     */
    public function getAllWithFilters(array $filters = [], int $per_page = 50)
    {
        $query = DailyVolume::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {
            if ($key !== 'customer_id' && $key !== 'customer_site_id' && $key !== 'created_at_from' && $key !== 'created_at_to' && $key !== 'updated_at_from' && $key !== 'updated_at_to' && $key !== 'volume') {
                continue;
            }
            switch ($key) {
                case 'created_at_from':
                    $query->whereDate('created_at', '>=', $value);
                    break;
                case 'created_at_to':
                    $query->whereDate('created_at', '<=', $value);
                    break;
                case 'updated_at_from':
                    $query->whereDate('updated_at', '>=', $value);
                    break;
                case 'updated_at_to':
                    $query->whereDate('updated_at', '<=', $value);
                    break;
                default:
                    // Apply other filters directly (e.g., status, customer_id)
                    $query->where($key, $value);
                    break;
            }
        }

        // Paginate the results
        return $query->paginate($per_page);
    }


    /**
     * Get paginated customer daily volumes.
     *
     * @param int $per_page Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $per_page = 50)
    {
        return DailyVolume::paginate($per_page);
    }

    /**
     * Find a daily volume entry by its ID.
     *
     * @param int $id The ID of the daily volume entry.
     * @return DailyVolume The found daily volume entry.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no entry is found.
     */
    public function getById(int $id): DailyVolume
    {
        return DailyVolume::findOrFail($id);
    }

    /**
     * Create a new daily volume entry.
     *
     * @param array $data The data for creating the daily volume.
     * @return DailyVolumeResource The resource of the newly created daily volume entry.
     * @throws \Throwable
     */
    public function create(array $data)
    {
        Log::info('Starting daily volume creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Check if form_field_answers is provided in JSON format
                if (isset($data['form_field_answers'])) {
                    // Decode the JSON data
                    $arrayData = json_decode($data['form_field_answers'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \InvalidArgumentException('Invalid JSON in form_field_answers');
                    }

                    // Optionally, prepare structured data if needed
                    $structuredData = [
                        "created_by" => $data['user_id'],
                        "created_at" => Carbon::parse($data["created_at"])->subDay(),
                    ];
                    foreach ($arrayData as $item) {
                        $structuredData[$item['key']] = $item['value'];
                    }

                    // Merge structured data with the main input data
                    $data = array_merge($data, $structuredData);
                }

                // Validate and create the Daily Volume entry
                $validatedData = $this->validateDailyVolume($data);
                $dailyVolume = DailyVolume::create($data);

                // Load relationships
                //$dailyVolume->refresh();
                //$dailyVolume->load(['customer', 'customer_site']);

                // Create a new Daily Volume resource

                if ($dailyVolume) {
                    $dailyVolumeQueues = config("nnpcreusable.GAS_CONSUMPTION_CREATED");
                    if (is_array($dailyVolumeQueues) && !empty($dailyVolumeQueues)) {
                        foreach ($dailyVolumeQueues as $queue) {
                            $queue = trim($queue);
                            if (!empty($queue)) {
                                Log::info("Dispatching daily volume creation event to queue: " . $queue);
                                GasConsumptionCreated::dispatch($dailyVolume->toArray())->onQueue($queue);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Unexpected error during daily volume creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }


    /**
     * Update an existing Daily Volume record.
     *
     * @param array $data The data to update the record with.
     * @return DailyVolumeResource The updated Daily Volume record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no record is found with the provided ID.
     * @throws ValidationException If the validation fails.
     */
    public function update(array $data): DailyVolumeResource
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting daily volume update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing Daily Volume record by its ID
            $dailyVolume = $this->getById($id);

            // Check if form_field_answers is provided in JSON format and decode if necessary
            if (isset($data['form_field_answers'])) {
                // Decode the JSON data
                $arrayData = json_decode($data['form_field_answers'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Invalid JSON in form_field_answers');
                }

                // Optionally, prepare structured data from the decoded array
                $structuredData = [];
                foreach ($arrayData as $item) {
                    $structuredData[$item['key']] = $item['value'];
                }

                // Merge structured data with the main input data
                $data = array_merge($data, $structuredData);
            }

            // Validate the data before performing the update
            $validatedData = $this->validateDailyVolume($data, true);

            // Perform the update on the existing Daily Volume record
            $dailyVolume->update($validatedData);

            // Load relationships
            $dailyVolume->load(['customer', 'customer_site']);

            // Create a new Daily Volume resource
            $resource = new DailyVolumeResource($dailyVolume);

            $dailyVolumeQueues = config("nnpcreusable.GAS_CONSUMPTION_UPDATED");
            if (is_array($dailyVolumeQueues) && !empty($dailyVolumeQueues)) {
                foreach ($dailyVolumeQueues as $queue) {
                    $queue = trim($queue);
                    if (!empty($queue)) {
                        Log::info("Dispatching daily volume update event to queue: " . $queue);
                        GasConsumptionUpdated::dispatch($resource)->onQueue($queue);
                    }
                }
            }

            Log::info('Daily volume updated successfully', ['id' => $dailyVolume->id]);

            return $resource;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during daily volume update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

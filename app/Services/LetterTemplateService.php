<?php

namespace App\Services;

use App\Models\LetterTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LetterTemplateService
{
    /**
     * Validate the letter template data.
     *
     * @param array $data
     * @param bool $isUpdate
     * @return array
     * @throws ValidationException
     */
    public function validateLetterTemplate(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'letter' => 'required|string|max:255',
            'status' => 'required|integer',
        ];

        // Add rules for update to allow id to be present
        if ($isUpdate) {
            $rules['id'] = 'required|exists:letter_templates,id';
        }

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get paginated letter templates with optional filtering.
     *
     * @param array $filters
     * @param int $per_page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $per_page = 50)
    {
        $query = LetterTemplate::query();

        // Apply filters
        foreach ($filters as $key => $value) {
            if (!in_array($key, ['id', 'letter', 'status'])) {
                continue;
            }
            $query->where($key, $value);
        }

        return $query->paginate($per_page);
    }

    /**
     * Find a letter template by its ID.
     *
     * @param int $id
     * @return LetterTemplate
     * @throws ModelNotFoundException
     */
    public function getById(int $id): LetterTemplate
    {
        return LetterTemplate::findOrFail($id);
    }

    /**
     * Create a new letter template.
     *
     * @param array $data
     * @return LetterTemplate
     * @throws \Throwable
     */
    public function create(array $data): LetterTemplate
    {
        Log::info('Starting letter template creation process', ['data' => $data]);

        return DB::transaction(function () use ($data) {
            try {
                // Validate and create the letter template
                $validatedData = $this->validateLetterTemplate($data);
                $letterTemplateCreated = LetterTemplate::create($validatedData);

                return $letterTemplateCreated;
            } catch (\Throwable $e) {
                Log::error('Unexpected error during letter template creation: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing letter template.
     *
     * @param array $data
     * @return LetterTemplate
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    public function update(array $data): LetterTemplate
    {
        // Retrieve the ID from the provided data
        $id = $data['id'] ?? null;

        if (!$id) {
            throw new \InvalidArgumentException('ID is required for update');
        }

        Log::info('Starting letter template update process', ['id' => $id, 'data' => $data]);

        try {
            // Find the existing letter template by its ID
            $letterTemplate = $this->getById($id);

            // Validate the data before performing the update
            $validatedData = $this->validateLetterTemplate($data, true);

            // Perform the update
            $letterTemplate->update($validatedData);

            Log::info('Letter template updated successfully', ['id' => $letterTemplate->id]);

            return $letterTemplate;
        } catch (\Throwable $e) {
            Log::error('Unexpected error during letter template update: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

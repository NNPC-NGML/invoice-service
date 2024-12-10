<?php

namespace App\Services;

use App\Models\Gcc;
use App\Models\GccApprovedByAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GccService
{
    /**
     * Validate the provided approval data.
     *
     * @param array $data The data to be validated.
     * @param bool $is_update Indicates if this is an update operation.
     * @return array The validated data.
     * @throws ValidationException If the validation fails.
     */
    public function create($data)
    {
        $model = new Gcc();
        if (!empty($data)) {
            $validator = Validator::make($data, [
                'with_vat' => 'required',
                'customer_id' => 'required',
                'customer_site_id' => 'required',
                'capex_recovery_amount' => 'required',
                'gcc_date' => 'required|date',
                'department_id' => 'required',
                'gcc_created_by' => 'required',
                'letter_id' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            return $model->create($data);
        }
    }
    public function getGccById($id)
    {
        if (!empty($id)) {

            $getGcc = Gcc::where('id', $id)->first();
            if (!$getGcc) {
                return false;
            }
            return $getGcc;
        }
    }
    public function getLastGccByCustomerAndCustomerSite($customer, $customerSite)
    {
        $getGcc = Gcc::where(['customer_id' => $customer, 'customer_site_id' => $customerSite])
            ->orderBy('created_at', 'desc') // Order by latest
            ->first();

        // If no record exists, return false
        if (!$getGcc) {
            return false;
        }

        return $getGcc;
    }
}

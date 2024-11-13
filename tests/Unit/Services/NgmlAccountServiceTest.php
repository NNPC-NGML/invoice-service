<?php

namespace Tests\Unit\Services;

use App\Models\NgmlAccount;
use App\Services\NgmlAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NgmlAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return NgmlAccountService
     */
    public function getService()
    {
        return new NgmlAccountService();
    }

    /**
     * Test validateNgmlAccount method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'bank_name' => 'Sample Bank',
            'bank_address' => '123 Sample Street, Sample City',
            'account_name' => 'John Doe',
            'account_number' => '12345678',
            'sort_code' => '12-34-56',
            'tin' => '123-45-6789',
        ];

        $validatedData = $service->validateNgmlAccount($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateNgmlAccount method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'bank_name' => null, // invalid
            'bank_address' => '', // invalid
            'account_name' => '', // invalid
            'account_number' => 'invalid_number', // invalid
            'sort_code' => 'not-a-sort-code', // invalid
            'tin' => 'invalid_tin', // invalid
        ];

        $service->validateNgmlAccount($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateNgmlAccount()
    {
        $service = $this->getService();

        $data = [
            'bank_name' => 'Sample Bank',
            'bank_address' => '123 Sample Street, Sample City',
            'account_name' => 'John Doe',
            'account_number' => '12345678',
            'sort_code' => '12-34-56',
            'tin' => '123-45-6789',
        ];

        $ngmlAccount = $service->create($data);

        $this->assertInstanceOf(NgmlAccount::class, $ngmlAccount);
        $this->assertEquals($data['bank_name'], $ngmlAccount->bank_name);
        $this->assertEquals($data['account_number'], $ngmlAccount->account_number);
    }

    /**
     * Test create method with invalid data.
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'bank_name' => 'Sample Bank',
            'bank_address' => '123 Sample Street, Sample City',
            'account_name' => 10292, // invalid_tin
            'account_number' => '12345678',
            'sort_code' => '12-34-56',
            'tin' => '772672762',
            'form_field_answers' => 'invalid_json',
        ];

        $service->create($data);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateNgmlAccount()
    {
        $service = $this->getService();

        $ngmlAccount = NgmlAccount::factory()->create([
            'bank_name' => 'Sample Bank',
            'bank_address' => '123 Sample Street, Sample City',
            'account_name' => 'John Doe',
            'account_number' => '12345678',
            'sort_code' => '12-34-56',
            'tin' => '123-45-6789',
        ]);

        $data = [
            'id' => $ngmlAccount->id,
            'account_name' => 'Jane Doe',
        ];

        $updatedNgmlAccount = $service->update($data);

        $this->assertEquals('Jane Doe', $updatedNgmlAccount->account_name);
    }

    /**
     * Test update method with invalid data.
     *
     * @return void
     */
    public function testUpdateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $ngmlAccount = NgmlAccount::factory()->create();

        $data = [
            'id' => $ngmlAccount->id,
            'account_name' => 10292, // invalid_tin
        ];

        $service->update($data);
    }

    /**
     * Test update method without an ID.
     *
     * @return void
     */
    public function testUpdateWithoutId()
    {
        $this->expectException(\InvalidArgumentException::class);

        $service = $this->getService();

        $data = [
            'account_name' => 'New Name',
        ];

        $service->update($data);
    }
}

<?php

namespace Tests\Unit\Services;

use App\Models\GccApprovedByCustomer;
use App\Services\GccApprovedByCustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GccApprovedByCustomersServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return GccApprovedByCustomerService
     */
    public function getService()
    {
        return new GccApprovedByCustomerService();
    }

    /**
     * Test validateGccApproval method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'customer_name' => 'John Doe',
            'signature' => 'sample_signature.png',
            'date' => '2024-01-01 12:00:00',
        ];

        $validatedData = $service->validateCustomerApproval($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateGccApproval method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'customer_name' => null, // invalid
            'signature' => '', // invalid
            'date' => 'invalid_date', // invalid
        ];

        $service->validateCustomerApproval($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateGccApproval()
    {
        $service = $this->getService();

        $data = [
            'customer_name' => 'Jane Smith',
            'signature' => 'signature_image.png',
            'date' => '2024-01-01 14:00:00',
        ];

        $gccApproval = $service->create($data);

        $this->assertInstanceOf(GccApprovedByCustomer::class, $gccApproval);
        $this->assertEquals($data['customer_name'], $gccApproval->customer_name);
        $this->assertEquals($data['signature'], $gccApproval->signature);
        $this->assertEquals($data['date'], $gccApproval->date->toDateTimeString());
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
            'customer_name' => 'Customer Name',
            'signature' => 'valid_signature.png',
            'date' => -100000,  //invalid data
        ];

        $service->create($data);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateGccApproval()
    {
        $service = $this->getService();

        $gccApproval = GccApprovedByCustomer::factory()->create([
            'customer_name' => 'Original Name',
            'signature' => 'original_signature.png',
            'date' => '2024-01-01 10:00:00',
        ]);

        $data = [
            'id' => $gccApproval->id,
            'customer_name' => 'Updated Name',
        ];

        $updatedGccApproval = $service->update($data);

        $this->assertEquals('Updated Name', $updatedGccApproval->customer_name);
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

        $gccApproval = GccApprovedByCustomer::factory()->create();

        $data = [
            'id' => $gccApproval->id,
            'customer_name' => '', // invalid
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
            'customer_name' => 'Some Name',
        ];

        $service->update($data);
    }
}

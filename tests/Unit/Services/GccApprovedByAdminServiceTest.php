<?php

namespace Tests\Unit\Services;

use App\Models\GccApprovedByAdmin;
use App\Models\InvoiceAdvice;
use App\Services\GccApprovedByAdminService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GccApprovedByAdminServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return GccApprovedByAdminService
     */
    public function getService()
    {
        return new GccApprovedByAdminService();
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
            'user_id' => 1,
            'invoice_advice_id' => 1,
            'date' => '2024-01-01 12:00:00',
        ];

        $validatedData = $service->validateApproval($data);

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
            'user_id' => null, // invalid
            'invoice_advice_id' => null, // invalid
            'date' => 'not_a_datetime', // invalid
        ];

        $service->validateApproval($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateGccApproval()
    {
        $service = $this->getService();

        $invoice_advice = InvoiceAdvice::factory()->create();
        $gcc = GccApprovedByAdmin::factory()->create();

        $data = [
            'user_id' => 1,
            'invoice_advice_id' => $invoice_advice->id,
            'date' => '2024-01-01 12:00:00',
            'id' => $gcc->id,
        ];

        $gccApproval = $service->create($data);

        $this->assertInstanceOf(GccApprovedByAdmin::class, $gccApproval);
        $this->assertEquals($data['user_id'], $gccApproval->user_id);
        $this->assertEquals($data['invoice_advice_id'], $gccApproval->invoice_advice_id);
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
            'user_id' => null, // invalid
            'invoice_advice_id' => 1,
            'date' => '2024-01-01 12:00:00',
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

        $gccApproval = GccApprovedByAdmin::factory()->create([
            'user_id' => 1,
            'invoice_advice_id' => 1,
            'date' => '2024-01-01 12:00:00',
        ]);

        $data = [
            'id' => $gccApproval->id,
            'date' => '2024-01-02 12:00:00',
        ];

        $updatedGccApproval = $service->update($data);

        $this->assertEquals('2024-01-02 12:00:00', $updatedGccApproval->date->toDateTimeString());
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

        $gccApproval = GccApprovedByAdmin::factory()->create();

        $data = [
            'id' => $gccApproval->id,
            'date' => 'not_a_datetime', // invalid
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
            'date' => '2024-01-01 12:00:00',
        ];

        $service->update($data);
    }
}

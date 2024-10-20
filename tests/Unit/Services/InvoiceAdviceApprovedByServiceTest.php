<?php

namespace Tests\Unit\Services;

use App\Models\InvoiceAdviceApprovedBy;
use App\Services\InvoiceAdviceApprovedByService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InvoiceAdviceApprovedByServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return InvoiceAdviceApprovedByService
     */
    public function getService()
    {
        return new InvoiceAdviceApprovedByService();
    }

    /**
     * Test validateInvoiceAdviceApprovedBy method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'user_id' => 1,
            'invoice_advice_id' => 1,
            'approval_for' => 1,
            'date' => '2024-01-01 12:00:00',
        ];

        $validatedData = $service->validateInvoiceAdviceApprovedBy($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateInvoiceAdviceApprovedBy method with invalid data.
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
            'approval_for' => 'not_an_integer', // invalid
            'date' => 'invalid_date', // invalid
        ];

        $service->validateInvoiceAdviceApprovedBy($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateInvoiceAdviceApprovedBy()
    {
        $service = $this->getService();

        $data = [
            'user_id' => 1,
            'invoice_advice_id' => 1,
            'approval_for' => 1,
            'date' => '2024-01-01 12:00:00',
        ];

        $invoiceAdviceApprovedBy = $service->create($data);

        $this->assertInstanceOf(InvoiceAdviceApprovedBy::class, $invoiceAdviceApprovedBy);
        $this->assertEquals($data['user_id'], $invoiceAdviceApprovedBy->user_id);
        $this->assertEquals($data['invoice_advice_id'], $invoiceAdviceApprovedBy->invoice_advice_id);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateInvoiceAdviceApprovedBy()
    {
        $service = $this->getService();

        $invoiceAdviceApprovedBy = InvoiceAdviceApprovedBy::factory()->create([
            'user_id' => 1,
            'invoice_advice_id' => 1,
            'approval_for' => 1,
            'date' => '2024-01-01 12:00:00',
        ]);

        $data = [
            'id' => $invoiceAdviceApprovedBy->id,
            'approval_for' => 2,
        ];

        $updatedInvoiceAdviceApprovedBy = $service->update($data);

        $this->assertEquals(2, $updatedInvoiceAdviceApprovedBy->approval_for);
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

        $invoiceAdviceApprovedBy = InvoiceAdviceApprovedBy::factory()->create();

        $data = [
            'id' => $invoiceAdviceApprovedBy->id,
            'date' => -1, // invalid
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
            'approval_for' => 2,
        ];

        $service->update($data);
    }
}

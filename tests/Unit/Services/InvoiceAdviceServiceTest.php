<?php

namespace Tests\Unit\Services;

use App\Models\InvoiceAdvice;
use App\Services\InvoiceAdviceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InvoiceAdviceServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return InvoiceAdviceService
     */
    public function getService()
    {
        return new InvoiceAdviceService();
    }

    /**
     * Test validateInvoiceAdvice method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'with_vat' => true,
            'customer_id' => 1,
            'customer_site_id' => 1,
            'capex_recovery_amount' => '1000.00',
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
            'department' => 'Gas Department Ajah',
            'gcc_created_by' => 1,
            'invoice_advice_created_by' => 1,
        ];

        $validatedData = $service->validateInvoiceAdvice($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateInvoiceAdvice method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'with_vat' => 'not_a_boolean', // invalid
            'customer_id' => null, // invalid
            'customer_site_id' => null, // invalid
            'capex_recovery_amount' => 'invalid_amount', // invalid
            'date' => 'invalid_date', // invalid
            'status' => 'not_an_integer', // invalid
        ];

        $service->validateInvoiceAdvice($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateInvoiceAdvice()
    {
        $service = $this->getService();

        $data = [
            'with_vat' => true,
            'customer_id' => 1,
            'customer_site_id' => 1,
            'capex_recovery_amount' => '1000.00',
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
            'department' => 'Gas Department Ajah',
            'gcc_created_by' => 1,
            'invoice_advice_created_by' => 1,
        ];

        $invoiceAdvice = $service->create($data);

        $this->assertInstanceOf(InvoiceAdvice::class, $invoiceAdvice);
        $this->assertEquals($data['with_vat'], $invoiceAdvice->with_vat);
        $this->assertEquals($data['capex_recovery_amount'], $invoiceAdvice->capex_recovery_amount);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateInvoiceAdvice()
    {
        $service = $this->getService();

        $invoiceAdvice = InvoiceAdvice::factory()->create([
            'with_vat' => true,
            'customer_id' => 1,
            'customer_site_id' => 1,
            'capex_recovery_amount' => '1000.00',
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
            'department' => 'Gas Department Ajah',
            'gcc_created_by' => 1,
            'invoice_advice_created_by' => 1,
        ]);

        $data = [
            'id' => $invoiceAdvice->id,
            'capex_recovery_amount' => '1500.00',
        ];

        $updatedInvoiceAdvice = $service->update($data);

        $this->assertEquals('1500.00', $updatedInvoiceAdvice->capex_recovery_amount);
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

        $invoiceAdvice = InvoiceAdvice::factory()->create();

        $data = [
            'id' => $invoiceAdvice->id,
            'capex_recovery_amount' => false, // invalid
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
            'capex_recovery_amount' => '2000.00',
        ];

        $service->update($data);
    }
}

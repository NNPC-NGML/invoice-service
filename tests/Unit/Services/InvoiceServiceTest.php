<?php

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return InvoiceService
     */
    public function getService()
    {
        return new InvoiceService();
    }

    /**
     * Test validateInvoice method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'invoice_number' => 'INV-001',
            'invoice_advice_id' => 1,
            'consumed_volume_amount_in_naira' => '10000',
            'consumed_volume_amount_in_dollar' => '25',
            'dollar_to_naira_convertion_rate' => '400',
            'vat_amount' => '2000',
            'total_volume_paid_for' => '12000',
            'status' => 1,
        ];

        $validatedData = $service->validateInvoice($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateInvoice method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'invoice_number' => '', // invalid
            'invoice_advice_id' => null, // invalid
            'consumed_volume_amount_in_naira' => 'invalid', // invalid
            'consumed_volume_amount_in_dollar' => 'invalid', // invalid
            'dollar_to_naira_convertion_rate' => 'invalid', // invalid
            'vat_amount' => 'invalid', // invalid
            'total_volume_paid_for' => 'invalid', // invalid
            'status' => 'not_an_integer', // invalid
        ];

        $service->validateInvoice($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateInvoice()
    {
        $service = $this->getService();

        $data = [
            'invoice_number' => 'INV-001',
            'invoice_advice_id' => 1,
            'consumed_volume_amount_in_naira' => '10000',
            'consumed_volume_amount_in_dollar' => '25',
            'dollar_to_naira_convertion_rate' => '400',
            'vat_amount' => '2000',
            'total_volume_paid_for' => '12000',
            'status' => 1,
        ];

        $invoice = $service->create($data);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($data['invoice_number'], $invoice->invoice_number);
        $this->assertEquals($data['consumed_volume_amount_in_naira'], $invoice->consumed_volume_amount_in_naira);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateInvoice()
    {
        $service = $this->getService();

        $invoice = Invoice::factory()->create([
            'invoice_number' => 'INV-001',
            'invoice_advice_id' => 1,
            'consumed_volume_amount_in_naira' => '10000',
            'consumed_volume_amount_in_dollar' => '25',
            'dollar_to_naira_convertion_rate' => '400',
            'vat_amount' => '2000',
            'total_volume_paid_for' => '12000',
            'status' => 1,
        ]);

        $data = [
            'id' => $invoice->id,
            'status' => 2, // update status
        ];

        $updatedInvoice = $service->update($data);

        $this->assertEquals(2, $updatedInvoice->status);
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

        $invoice = Invoice::factory()->create();

        $data = [
            'id' => $invoice->id,
            'consumed_volume_amount_in_naira' => 'invalid', // invalid
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
            'status' => 2,
        ];

        $service->update($data);
    }
}

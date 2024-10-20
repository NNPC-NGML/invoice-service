<?php

namespace Tests\Unit\Services;

use App\Models\InvoiceAdviceListItem;
use App\Services\InvoiceAdviceListItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InvoiceAdviceListItemServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the service instance.
     *
     * @return InvoiceAdviceListItemService
     */
    public function getService()
    {
        return new InvoiceAdviceListItemService();
    }

    /**
     * Test validateInvoiceAdviceListItem method with valid data.
     *
     * @return void
     */
    public function testValidateWithValidData()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => '5000',
            'inlet' => '100',
            'outlet' => '90',
            'take_or_pay_value' => '1000',
            'allocation' => '6000',
            'daily_target' => '4500',
            'nomination' => '4200',
            'daily_gas_id' => 1,
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
        ];

        $validatedData = $service->validateInvoiceAdviceListItem($data);

        $this->assertEquals($data, $validatedData);
    }

    /**
     * Test validateInvoiceAdviceListItem method with invalid data.
     *
     * @return void
     */
    public function testValidateWithInvalidData()
    {
        $this->expectException(ValidationException::class);

        $service = $this->getService();

        $data = [
            'customer_id' => null, // invalid
            'customer_site_id' => null, // invalid
            'volume' => 'invalid_volume', // invalid
            'inlet' => 'invalid_inlet', // invalid
            'outlet' => 'invalid_outlet', // invalid
            'take_or_pay_value' => 'invalid_value', // invalid
            'allocation' => 'invalid_allocation', // invalid
            'daily_target' => 'invalid_target', // invalid
            'nomination' => 'invalid_nomination', // invalid
            'daily_gas_id' => null, // invalid
            'date' => 'invalid_date', // invalid
            'status' => 'not_an_integer', // invalid
        ];

        $service->validateInvoiceAdviceListItem($data);
    }

    /**
     * Test create method with valid data.
     *
     * @return void
     */
    public function testCreateInvoiceAdviceListItem()
    {
        $service = $this->getService();

        $data = [
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => '5000',
            'inlet' => '100',
            'outlet' => '90',
            'take_or_pay_value' => '1000',
            'allocation' => '6000',
            'daily_target' => '4500',
            'nomination' => '4200',
            'daily_gas_id' => 1,
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
        ];

        $invoiceAdviceListItem = $service->create($data);

        $this->assertInstanceOf(InvoiceAdviceListItem::class, $invoiceAdviceListItem);
        $this->assertEquals($data['volume'], $invoiceAdviceListItem->volume);
        $this->assertEquals($data['inlet'], $invoiceAdviceListItem->inlet);
        $this->assertEquals($data['outlet'], $invoiceAdviceListItem->outlet);
    }

    /**
     * Test update method with valid data.
     *
     * @return void
     */
    public function testUpdateInvoiceAdviceListItem()
    {
        $service = $this->getService();

        $invoiceAdviceListItem = InvoiceAdviceListItem::factory()->create([
            'customer_id' => 1,
            'customer_site_id' => 1,
            'volume' => '5000',
            'inlet' => '100',
            'outlet' => '90',
            'take_or_pay_value' => '1000',
            'allocation' => '6000',
            'daily_target' => '4500',
            'nomination' => '4200',
            'daily_gas_id' => 1,
            'date' => '2024-01-01 12:00:00',
            'status' => 1,
        ]);

        $data = [
            'id' => $invoiceAdviceListItem->id,
            'volume' => '6000',
        ];

        $updatedInvoiceAdviceListItem = $service->update($data);

        $this->assertEquals('6000', $updatedInvoiceAdviceListItem->volume);
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

        $invoiceAdviceListItem = InvoiceAdviceListItem::factory()->create();

        $data = [
            'id' => $invoiceAdviceListItem->id,
            'volume' => 'invalid_volume', // invalid
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
            'volume' => '5500',
        ];

        $service->update($data);
    }
}

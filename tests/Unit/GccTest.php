<?php

namespace Tests\Unit;

use Carbon\Carbon;
use App\Models\Gcc;
use Tests\TestCase;
use App\Services\GccService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GccTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_that_a_new_gcc_can_be_created(): void
    {
        $data = [
            "with_vat" => true,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "capex_recovery_amount" => 200,
            "gcc_date" => Carbon::now(), //->format('Y-m-d'),
            "department_id" => 1,
            "gcc_created_by" => 1,
            "letter_id" => 1,
            "status" => 0,
        ];
        $service = new GccService();
        $createGcc = $service->create($data);
        $this->assertDatabaseHas('gccs', $createGcc->toArray());
    }
    public function test_that_gcc_can_be_fetched_by_id(): void
    {
        $data = [
            "with_vat" => true,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "capex_recovery_amount" => 200,
            "gcc_date" => Carbon::now(), //->format('Y-m-d'),
            "department_id" => 1,
            "gcc_created_by" => 1,
            "letter_id" => 1,
            "status" => 0,
        ];
        $createGcc = Gcc::factory()->create($data);
        $service = new GccService();
        $getGcc = $service->getGccById($createGcc->id);
        $this->assertEquals($getGcc->id, $createGcc->id);
    }
    public function test_that_last_gcc_can_be_fetched_by_customer_and_customer_site_id(): void
    {
        $data = [
            "with_vat" => true,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "capex_recovery_amount" => 200,
            "gcc_date" => Carbon::now(), //->format('Y-m-d'),
            "department_id" => 1,
            "gcc_created_by" => 1,
            "letter_id" => 1,
            "status" => 0,
        ];
        $createGcc = Gcc::factory()->create($data);
        $service = new GccService();
        $getGcc = $service->getLastGccByCustomerAndCustomerSite($createGcc->customer_id, $createGcc->customer_site_id);
        $this->assertEquals($getGcc->id, $createGcc->id);
    }
}

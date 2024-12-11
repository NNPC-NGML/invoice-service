<?php

namespace Tests\Feature;

use App\Models\Gcc;
use Tests\TestCase;
use App\Models\DailyVolume;
use Illuminate\Support\Carbon;
use App\Models\InvoiceAdviceListItem;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GccTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_that_gcc_init_sends_the_right_content_if_the_customer_gcc_for_that_month_does_not_exist(): void
    {
        // create two invoice advice for the previous month
        DailyVolume::factory()->create([

            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);
        DailyVolume::factory()->create([

            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth(),
            "updated_at" => Carbon::now()->subMonth(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);
        $this->actingAsAuthenticatedTestUser();
        $response = $this->postJson('/api/gcc-init', [
            "customer_id" => 1,
            "customer_site_id" => 1,
        ]);
        $response->assertStatus(200)->assertJsonStructure([
            "status",
            "data" => [
                "list_item",
                "gcc",
                "invoice_advice",
                "invoice",
            ],
        ])->assertJson([
            "status" => "success",
            "data" => [
                "list_item" => [
                    [
                        "id" => 1,
                        "customer_id" => 1,
                        "customer_site_id" => 1,
                        "volume" => 1000.0,
                        "inlet_pressure" => 200.0,
                        "outlet_pressure" => 20.0,
                        "allocation" => 200.0,
                        "nomination" => 200.0,
                        "status" => 1,
                        "created_by" => 1,
                        "approved_by" => 1,
                    ],
                    [
                        "id" => 2,
                        "customer_id" => 1,
                        "customer_site_id" => 1,
                        "volume" => 1000.0,
                        "inlet_pressure" => 200.0,
                        "outlet_pressure" => 20.0,
                        "allocation" => 200.0,
                        "nomination" => 200.0,
                        "status" => 1,
                        "created_by" => 1,
                        "approved_by" => 1,

                    ]
                ],
                "gcc" => null,
                "invoice_advice" => null,
                "invoice" => null,
            ],
        ]);
    }

    //public function test_that_gcc_init_sends_the_right_content_if_the_customer_has_no_gcc_record(): void {}

    public function test_that_gcc_init_sends_the_right_content_if_the_customer_has_a_gcc_record_for_that_month(): void
    {
        // create two invoice advice for the previous month
        $dailyVolume = DailyVolume::factory()->create([
            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);

        $createGcc = Gcc::factory()->create([
            "customer_id" => 1,
            "customer_site_id" => 1,
            "gcc_date" => Carbon::now()->subMonth(),
            "gcc_created_by" => 1,
            "department_id" => 1,
            "capex_recovery_amount" => 200,
            "with_vat" => true,
            "status" => 0,
        ]);
        // create invoice advice list item 
        InvoiceAdviceListItem::factory()->create([
            "gcc_id" => $createGcc->id,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "daily_volume_id" => 1,
            "volume" => 1000,
            "inlet" => 200,
            "outlet" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "original_date" => Carbon::now()->subMonth()->subDay(),
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
        ]);

        $this->actingAsAuthenticatedTestUser();
        $response = $this->postJson('/api/gcc-init', [
            "customer_id" => 1,
            "customer_site_id" => 1,
        ]);
        $response->assertStatus(200)->assertJsonStructure([
            "status",
            "data" => [
                "list_item",
                "gcc",
                "invoice_advice",
                "invoice",
            ],
        ])->assertJson([
            "status" => "success",
            "data" => [
                "list_item" => [
                    [
                        "id" => 1,
                        "customer_id" => 1,
                        "customer_site_id" => 1,
                        "daily_volume_id" => 1,
                        "volume" => "1000",
                        "inlet" => "200",
                        "outlet" => "20",
                        "allocation" => "200",
                        "nomination" => "200",
                        "gcc_id" => 1,
                        "status" => 1,

                    ]
                ],
                "gcc" => [
                    "id" => 1,
                    "with_vat" => 1,
                    "customer_id" => 1,
                    "customer_site_id" => 1,
                    "capex_recovery_amount" => "200",
                    "department_id" => 1,
                    "gcc_created_by" => 1,
                    "letter_id" => 1,
                ],
                "invoice_advice" => null,
                "invoice" => null,
            ],
        ]);
    }

    public function test_that_gcc_can_be_created(): void
    {
        $dailyVolume = DailyVolume::factory()->create([
            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);
        $data = [
            "customer_id" => 1,
            "customer_site_id" => 1,
            "list_item" => json_encode([$dailyVolume->toArray()]),
        ];
        $this->actingAsAuthenticatedTestUser();
        $response = $this->postJson('/api/gcc/create', $data);
        $this->assertDatabaseCount('gccs', 1);
        $this->assertDatabaseCount('invoice_advice_list_items', 1);
        $response->assertStatus(201)->assertJsonStructure([
            "status",
            "data" => [
                "list_item",
                "gcc",
                "invoice_advice",
                "invoice",
            ],
        ]);
    }

    public function test_that_gcc_can_be_approved_by_admin(): void
    {
        // create two invoice advice for the previous month
        $dailyVolume = DailyVolume::factory()->create([
            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);

        $createGcc = Gcc::factory()->create([
            "customer_id" => 1,
            "customer_site_id" => 1,
            "gcc_date" => Carbon::now()->subMonth(),
            "gcc_created_by" => 1,
            "department_id" => 1,
            "capex_recovery_amount" => 200,
            "with_vat" => true,
            "status" => 0,
        ]);
        // create invoice advice list item 
        InvoiceAdviceListItem::factory()->create([
            "gcc_id" => $createGcc->id,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "daily_volume_id" => 1,
            "volume" => 1000,
            "inlet" => 200,
            "outlet" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "original_date" => Carbon::now()->subMonth()->subDay(),
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
        ]);

        $this->actingAsAuthenticatedTestUser();
        $response = $this->getJson('/api/gcc/admin/approve/' . $createGcc->id);
        $response->assertStatus(200);
        $this->assertDatabaseHas("gcc_approved_by_admins", ["gcc_id" => $createGcc->id, "user_id" => 1]);
        $this->assertDatabaseHas("gccs", ["status" => Gcc::GCCAPPROVEDBYADMIN]);
        $response->assertJsonStructure([
            "status",
            "data" => [
                "list_item",
                "gcc",
                "invoice_advice",
                "invoice",
            ],
        ])->assertJson([
            "status" => "success",
            "data" => [
                "list_item" => [
                    [
                        "id" => 1,
                        "customer_id" => 1,
                        "customer_site_id" => 1,
                        "daily_volume_id" => 1,
                        "volume" => "1000",
                        "inlet" => "200",
                        "outlet" => "20",
                        "allocation" => "200",
                        "nomination" => "200",
                        "gcc_id" => 1,
                        "status" => 1,

                    ]
                ],
                "gcc" => [
                    "id" => 1,
                    "with_vat" => 1,
                    "customer_id" => 1,
                    "customer_site_id" => 1,
                    "capex_recovery_amount" => "200",
                    "department_id" => 1,
                    "gcc_created_by" => 1,
                    "letter_id" => 1,
                ],
                "invoice_advice" => null,
                "invoice" => null,
            ],
        ]);
    }

    public function test_that_gcc_can_be_approved_by_customer(): void
    {
        // create two invoice advice for the previous month
        $dailyVolume = DailyVolume::factory()->create([
            "inlet_pressure" => 200,
            "outlet_pressure" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "created_by" => 1,
            "approved_by" => 1,
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
            "customer_id" => 1,
            "customer_site_id" => 1,
            "volume" => 1000,
        ]);

        $createGcc = Gcc::factory()->create([
            "customer_id" => 1,
            "customer_site_id" => 1,
            "gcc_date" => Carbon::now()->subMonth(),
            "gcc_created_by" => 1,
            "department_id" => 1,
            "capex_recovery_amount" => 200,
            "with_vat" => true,
            "status" => 0,
        ]);
        // create invoice advice list item 
        InvoiceAdviceListItem::factory()->create([
            "gcc_id" => $createGcc->id,
            "customer_id" => 1,
            "customer_site_id" => 1,
            "daily_volume_id" => 1,
            "volume" => 1000,
            "inlet" => 200,
            "outlet" => 20,
            "allocation" => 200,
            "nomination" => 200,
            "status" => 1,
            "original_date" => Carbon::now()->subMonth()->subDay(),
            "created_at" => Carbon::now()->subMonth()->subDay(),
            "updated_at" => Carbon::now()->subMonth()->subDay(),
        ]);

        $this->actingAsUnAuthenticatedTestUser();
        $data = [
            "gcc_id" => $createGcc->id,
            "customer_name" => "test",
            "signature" => "test",
        ];
        $response = $this->postJson('/api/gcc/customer/approve/' . $createGcc->id, $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas("gcc_approved_by_customers", ["gcc_id" => $createGcc->id, "customer_name" => "test"]);
        $this->assertDatabaseHas("gccs", ["status" => Gcc::GCCAPPROVEDBYCUSTOMER]);
        $response->assertJsonStructure([
            "status",
            "data" => [
                "list_item",
                "gcc",
                "invoice_advice",
                "invoice",
            ],
        ])->assertJson([
            "status" => "success",
            "data" => [
                "list_item" => [
                    [
                        "id" => 1,
                        "customer_id" => 1,
                        "customer_site_id" => 1,
                        "daily_volume_id" => 1,
                        "volume" => "1000",
                        "inlet" => "200",
                        "outlet" => "20",
                        "allocation" => "200",
                        "nomination" => "200",
                        "gcc_id" => 1,
                        "status" => 1,

                    ]
                ],
                "gcc" => [
                    "id" => 1,
                    "with_vat" => 1,
                    "customer_id" => 1,
                    "customer_site_id" => 1,
                    "capex_recovery_amount" => "200",
                    "department_id" => 1,
                    "gcc_created_by" => 1,
                    "letter_id" => 1,
                ],
                "invoice_advice" => null,
                "invoice" => null,
            ],
        ]);
    }
}
